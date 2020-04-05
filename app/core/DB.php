<?php namespace Manevia;

    use mysqli;
    use mysqli_result;
    use mysqli_stmt;

    /********************************************************************************
     * MYSQLI DATABASE & PHP SESSION HANDLER
     * @author Jabari J. Hunt <jabari@jabari.net>
     *
     * Class that handles database connections, queries, and backups as well as
     * access and storage of session MySQL.
     *
     * PHP version 5.6+
     *
     ********************************************************************************
     * REQUIRED DB TABLE FOR MYSQL SESSIONS
     ********************************************************************************
     *
     * If using sessions, the following table must be created in your database:
     * NOTE: This can be done in the CLI build script.
     *
     * CREATE TABLE `sessions` (
     *   `id` varchar(100) NOT NULL default '',
     *     `data` text NOT NULL,
     *     `expires` int unsigned NOT NULL default '0',
     *     PRIMARY KEY  (`id`)
     * ) ENGINE=InnoDB;
     *
     ********************************************************************************
     * RECOMMENDED PHP.INI SETTINGS FOR SESSIONS
     ********************************************************************************
     *
     * - Handle session garbage collection with a cron (for better overall performance)
     * session.gc_probability = 0
     *
     * - If using Redis or memcached for session management, set the following accordingly
     * session.save_handler = redis
     * session.save_path = "tcp://127.0.0.1:6379?auth=YourSuperSecretPassword"
     *
     ********************************************************************************/

        final class DB {

            /********************************************************************************
             * CONNECTION AND SESSION VARIABLES
             * @var string $host Database Server
             * @var string $database Database Instance
             * @var string $user Database Username
             * @var string $password Database Password
             * @var string $socket Database Socket
             * @var boolean $useDBSessions Value designating if sessions should be used
             * @var string $sessionName Session Name
             * @var int $expires Session Expiration Time
             ********************************************************************************/

                // DATABASE CONNECTION VARIABLES

                    private static $host;
                    private static $database;
                    private static $user;
                    private static $password;
                    private static $socket;

                // SESSION VARIABLES

                    private static $useDBSessions;
                    //private static $sessionName;
                    private static $sessionExpires;

            /********************************************************************************
             * CLASS VARIABLES
             * @var object $db MySQLi instance
             * @var object $instance Singleton instance of this class
             ********************************************************************************/

                private static $db;
                private static $instance = NULL;

            /********************************************************************************
             * CLASS CONSTANTS
             * @var integer DATA_TYPE_INTEGER  - tinyint, smallint, mediumint, int, bigint, bit
             * @var integer DATA_TYPE_REAL     - float, double, decimal
             * @var integer DATA_TYPE_TEXT     - char, varchar, tinytext, text, mediumtext, longtext
             * @var integer DATA_TYPE_BINARY   - binary, varbinary, blob, tinyblob, mediumblob, longblob
             * @var integer DATA_TYPE_TEMPORAL - date, time, year, datetime, timestamp
             * @var integer DATA_TYPE_SPATIAL  - point, linestring, polygon, geometry, multipoint, multilinestring, multipolygon, geometrycollection
             * @var integer DATA_TYPE_OTHER    - enum, set
             ********************************************************************************/

                const DATA_TYPE_INTEGER  = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'bit'];
                const DATA_TYPE_REAL     = ['float', 'double', 'decimal'];
                const DATA_TYPE_TEXT     = ['char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext'];
                const DATA_TYPE_BINARY   = ['binary', 'varbinary', 'blob', 'tinyblob', 'mediumblob', 'longblob'];
                const DATA_TYPE_TEMPORAL = ['date', 'time', 'year', 'datetime', 'timestamp'];
                const DATA_TYPE_SPATIAL  = ['point', 'linestring', 'polygon', 'geometry', 'multipoint', 'multilinestring', 'multipolygon', 'geometrycollection'];
                const DATA_TYPE_OTHER    = ['enum', 'set'];

            /********************************************************************************
             * SINGLETON INSTANCE METHOD
             * @return mysqli
             ********************************************************************************/

                final private static function get(): mysqli {

                    if(self::$instance === NULL) {self::$instance = new DB();}
                    return self::$db;

                }

            /********************************************************************************
             * CONSTRUCTOR
             ********************************************************************************/

                final private function __construct() {

                    // SET DATABASE AND SESSION CLASS VARIABLES

                        self::$host           = getenv('DATABASE_HOST');
                        self::$database       = getenv('DATABASE_NAME');
                        self::$user           = getenv('DATABASE_USER');
                        self::$password       = getenv('DATABASE_PASSWORD');
                        self::$useDBSessions  = (boolean) getenv('DATABASE_SESSION_STORE_IN_DB');
                        self::$sessionExpires = (integer) getenv('DATABASE_SESSION_EXPIRES');

                        if (
                            self::$host === 'localhost' &&
                            !empty(getenv('DATABASE_SOCKET')) &&
                            getenv('DATABASE_SOCKET') !== 'NULL'
                        ) {
                            self::$socket = getenv('DATABASE_SOCKET');
                        } else {
                            self::$socket = NULL;
                        }

                    // CONNECT TO DATABASE | CHECK CONNECTION

                        self::$db = self::$socket === NULL ? new mysqli(self::$host, self::$user, self::$password, self::$database) : new mysqli(NULL, self::$user, self::$password, self::$database, NULL, self::$socket);

                        if (self::$db->connect_error === TRUE) {
                            die ('<b style="color: #F00;">COULD NOT CONNECT TO THE DATABASE SERVER</b>');
                        }

                    // START SESSION IF REQUESTED

                        if (self::$useDBSessions) {

                            // RUN SET SESSION HANDLER METHOD | START SESSION

                                session_set_save_handler (
                                    [$this, 'openSession'],
                                    [$this, 'closeSession'],
                                    [$this, 'readSession'],
                                    [$this, 'writeSession'],
                                    [$this, 'destroySession'],
                                    [$this, 'gcSession']
                                );

                                session_start();

                        }

                }

            /********************************************************************************
             * DESTRUCTOR
             ********************************************************************************/

                final public function __destruct() {

                    // CLOSE SESSION | CLOSE DATABASE

                        if (self::$db instanceof mysqli) {

                            if (self::$useDBSessions) {
                                session_write_close();
                            }

                            self::$db->close();

                        }

                }

            /********************************************************************************
             * PUBLIC DB METHODS -> BACKUP | SET INSTANCE | PREPARE | QUERY
             ********************************************************************************/

                /********************************************************************************
                 * BACKUPMETHOD
                 * @param string $directory
                 * @return void
                 ********************************************************************************/

                    final public static function backup(string $directory): void {

                        $user     = self::$user;
                        $password = self::$password;
                        $database = self::$database;
                        $location = rtrim($directory, '/') . "/{$database}-" . date('Ymd') . '_' . time() . '.sql';

                        exec("mysqldump --user='{$user}' --password='{$password}' --single-transaction --routines --triggers {$database} > {$location}");

                    }

                /********************************************************************************
                 * INITIALIZE METHOD
                 * @return void
                 ********************************************************************************/

                    final public static function initialize(): void {
                        self::get();
                    }

                /********************************************************************************
                 * PREPARE METHOD
                 * @param string $query
                 * @return mysqli_stmt
                 ********************************************************************************/

                    final public static function prepare(string $query): mysqli_stmt {
                        return self::get()->prepare($query);
                    }

                /********************************************************************************
                 * QUERY METHOD
                 * @param string $query
                 * @return mysqli_result
                 ********************************************************************************/

                    final public static function query(string $query): mysqli_result {
                        return self::get()->query($query);
                    }

            /********************************************************************************
             * SANITIZE METHOD
             * Used to sanitize individual field values for database insertion.
             * @param mixed $value The value to be sanitized.
             * @param string $dataType The DB datatype of the passed value
             * @return mixed
             ********************************************************************************/

                final public static function sanitize($value, string $dataType = NULL) {

                    // SANITIZE BASED ON FILTER TYPE | RETURN VALUE

                        if ($dataType !== NULL) {

                            if ($dataType === self::DATA_TYPE_INTEGER) {
                                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                            } else if ($dataType === self::DATA_TYPE_TEXT) {
                                $value = filter_var($value, FILTER_SANITIZE_STRING);
                            } else if ($dataType === self::DATA_TYPE_REAL) {
                                $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            } else {
                                $value = filter_var($value, FILTER_SANITIZE_STRING);
                            }

                        } else {
                            $value = filter_var($value, FILTER_SANITIZE_STRING);
                        }

                        return trim($value);

                }

            /********************************************************************************
             * SESSION HANDLER METHODS -> OPEN | CLOSE | READ | WRITE | DESTROY | GARBAGE COLLECTION
             * NOTE: BOTH close() AND destroy($session_id) MUST BE PUBLIC IF session_destroy() IS USED IN SCRIPTS!!!
             ********************************************************************************/

                final public function openSession(): bool {return TRUE;}

                final public function closeSession(): bool {return TRUE;}

                final public function readSession($sessionId) {

                    // SET CURRENT AND EXPIRATION TIME

                        $now     = time();
                        $expires = $now + (self::$sessionExpires * 60);

                    // QUERY DATABASE FOR SESSION DATA
                    // PROCESS SESSION IF ONE WAS RETURNED -> GET SESSION DATA | SET NEW EXPIRATION
                    // RETURN SESSION DATA

                        $result = self::$db->query("SELECT data FROM sessions WHERE id = '{$sessionId}' AND expires > {$now}");

                        if ($result->num_rows == 1) {

                            // GET SESSION DATA | SET NEW EXPIRATION

                                $session = $result->fetch_assoc();
                                $data    = $session['data'];

                                self::$db->query("UPDATE sessions SET expires = {$expires} WHERE id = '{$sessionId}'");

                        } else {
                            $data = '';
                        }

                        return $data;

                }

                final public function writeSession($sessionId, $sessionData) {

                    // SET INITIAL VARIABLES | REPLACE/INSERT SESSION DATA IN DATABASE -> RETURN RESULT

                        $expires        = time() + (self::$sessionExpires * 60);
                        $dbSessionData  = self::$db->real_escape_string($sessionData);

                        return self::$db->query("INSERT INTO sessions (id, data, expires) VALUES ('{$sessionId}', '{$dbSessionData}', '{$expires}') ON DUPLICATE KEY UPDATE data = '{$dbSessionData}', expires = {$expires}");

                }

                final public function destroySession($sessionId) {
                    return self::$db->query("DELETE FROM sessions WHERE id = {$sessionId}");
                }

                final public function gcSession() {
                    return self::$db->query('DELETE FROM sessions WHERE expires <= UNIX_TIMESTAMP()');
                }

            /********************************************************************************
             * SESSION CONVENIENCE METHOD
             * Used to start session garbage collection. Intended to be called from a cron
             * job, but can be called from anywhere.
             * @return void
             ********************************************************************************/

                final public static function startSessionGarbageCollection(): void {

                    self::get();
                    self::$instance->gcSession();

                }

        }
?>
