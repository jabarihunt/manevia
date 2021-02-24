<?php namespace Manevia;

    use Dotenv\Dotenv;

    /********************************************************************************
     * AUTO LOAD | INSTANTIATE REQUIRED LIBRARIES -> DOTENV | DB
     * START SESSIONS
     ********************************************************************************/

        require(__DIR__ . '/../../vendor/autoload.php');

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../manevia/');
        $dotenv->load();

        DB::initialize();

    /********************************************************************************
     * PHP CLI BASE MODEL BUILDER
     * PHP version 7.1+
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class BaseModelBuilder {

            /********************************************************************************
             * CLASS CONSTANTS
             * @var array SEARCH Array of place holders in BaseModel.php
             ********************************************************************************/

                const SEARCH = [
                    '[MODEL_NAME]',
                    '[MODEL_NAME_FIRST_LETTER_LOWERCASE]',
                    '[MODEL_NAME_UPPERCASE]',
                    '[CLASS_VARIABLES]',
                    '[CLASS_CONSTANT_DATA_TYPES]',
                    '[CLASS_CONSTANT_REQUIRED_FIELDS]',
                    '[TABLE_NAME]',
                    '[PRIMARY_KEY]',
                    '[GETTERS]',
                    '[TABLE_NAME_FORMATTED]',
                    '[ALL_COLUMN_NAMES]',
                    '[CREATE_METHOD_VALIDATION_CRITERIA]',
                    '[CREATE_METHOD_COLUMN_NAMES]',
                    '[CREATE_QUERY_COLUMN_PLACEHOLDERS]',
                    '[CREATE_METHOD_BIND_TYPES]',
                    '[CREATE_METHOD_BIND_DATA_STRING]'
                ];

            /********************************************************************************
             * CLASS VARIABLES
             * @var string $baseModel Holds base model text
             * @var array $replace Array of values to use in BaseModel.php (for each model)
             ********************************************************************************/

                private $baseModel;
                private $model;

                private $replace = [
                    'modelName'                      => '',
                    'modelNameFirstLetterLowercase'  => '',
                    'modelNameUppercase'             => '',
                    'classVariables'                 => '',
                    'classConstantDataTypes'         => '',
                    'classConstantRequiredFields'    => '',
                    'tableName'                      => '',
                    'primaryKey'                     => '',
                    'getters'                        => '',
                    'tableNameFormatted'             => '',
                    'allColumnNames'                 => '',
                    'createMethodValidationCriteria' => '',
                    'createQueryColumnNames'         => '',
                    'createQueryColumnPlaceholders'  => '',
                    'createMethodBindTypes'          => '',
                    'createMethodBindDataString'     => ''
                ];

            /********************************************************************************
             * CLASS CONSTRUCTOR AND DESTRUCTOR
             ********************************************************************************/

                public function __construct() {

                    // GET BASE MODEL | GET TABLE DATA

                        $this->prompt("\nStarting Base Model Builder...\n", FALSE);

                        $this->baseModel = file_get_contents(__DIR__ . '/BaseModel.php-distro');

                        if (!empty($this->baseModel)) {
                            $this->prompt('Retrieved base model template');
                        }

                        $this->model = file_get_contents(__DIR__ . '/Model.php-distro');

                        if (!empty($this->model)) {
                            $this->prompt('Retrieved model template');
                        }

                        $tableNames = $this->getTables();

                        if (is_array($tableNames) && count($tableNames) > 0) {
                            $this->prompt('Preparing to build ' . count($tableNames) . ' table(s)');
                        }

                    // BUILD BASE MODEL FOR EACH TABLE AND SAVE

                        foreach ($tableNames as $tableName) {

                            // CREATE MODEL DATA | PROMPT USER | RESET REPLACE ARRAY

                                $tableBuilt = $this->buildBaseModel($tableName);
                                $tableBuilt ? $this->prompt("COMPLETE: {$tableName}") : $this->prompt("ERROR: {$tableName}");
                                $this->resetReplaceArray();

                        }

                }

                public function __destruct() {
                    $this->prompt("\n", FALSE);
                }

            /********************************************************************************
             * GET TABLES METHOD
             * @return array
             ********************************************************************************/

                private function getTables(): array {

                    // SET INITIAL VARIABLES | GET TABLE NAMES | RETURN TABLES

                        $tableNames = [];
                        $results    = DB::query('SHOW TABLES');

                        while($row = $results->fetch_row()) {

                            if ($row[0] != 'sessions') {
                                $tableNames[] = $row[0];
                            }

                        }

                        return $tableNames;

                }

            /********************************************************************************
             * BUILD BASE MODEL METHOD
             * @param string $tableName
             * @return boolean
             ********************************************************************************/

                private function buildBaseModel(string $tableName): bool {

                    // GET TABLE COLUMN INFO | SET INITIAL RETURN VALUE

                        $results    = DB::query("DESCRIBE {$tableName}");
                        $modelBuilt = FALSE;

                    // SET INITIAL REPLACE VARIABLES

                        $this->replace['modelName']                     = Utilities::snakeToCamel($tableName, TRUE);
                        $this->replace['modelName']                     = Utilities::pluralToSingular($this->replace['modelName']);
                        $this->replace['modelNameFirstLetterLowercase'] = lcfirst($this->replace['modelName']);
                        $this->replace['modelNameUppercase']            = strtoupper($this->replace['modelName']);
                        $this->replace['tableName']                     = $tableName;

                    // LOOP THROUGH COLUMNS AND SET REMAINING VARIABLES

                    /* EXAMPLE COLUMN DATA
                    *
                    *   array(6) {
                    *    ["Field"]=>
                    *    string(2) "id"
                    *    ["Type"]=>
                    *    string(12) "varchar(100)"
                    *    ["Null"]=>
                    *    string(2) "NO"
                    *    ["Key"]=>
                    *    string(3) "PRI"
                    *    ["Default"]=>
                    *    string(0) ""
                    *    ["Extra"]=>
                    *    string(0) ""
                    */
                        while($column = $results->fetch_assoc()) {

                            // DO ANY VALUE PREP THAT IS REQUIRED

                                if (strpos($column['Type'], '(') !== FALSE) {
                                    $column['Type'] = stristr($column['Type'], '(', TRUE);
                                }

                            // SET REMAINING REPLACE VARIABLES

                                $this->replace['classVariables']                .= "                protected \${$column['Field']};\n";
                                $this->replace['classConstantDataTypes']        .= "                    '{$column['Field']}' => '{$column['Type']}',\n";
                                $this->replace['getters']                       .= '                final public function get' . Utilities::snakeToCamel($column['Field'], TRUE) . "() {return \$this->{$column['Field']};}\n";
                                $this->replace['allColumnNames']                .= "`{$column['Field']}`, ";

                                if (strtolower($column['Key']) != 'pri') {

                                    $this->replace['createQueryColumnNames']         .= "`{$column['Field']}`, ";
                                    $this->replace['createQueryColumnPlaceholders'] .= '?, ';
                                    $this->replace['createMethodBindDataString']    .= "\$data['{$column['Field']}'], ";

                                    if (strtolower($column['Null']) == 'no') {
                                        $this->replace['createMethodValidationCriteria'] .= "                            !empty(\$data['{$column['Field']}']) &&\n";
                                    }

                                    if (in_array($column['Type'], DB::DATA_TYPE_INTEGER)) {
                                        $this->replace['createMethodBindTypes'] .= 'i';
                                    } else {
                                        $this->replace['createMethodBindTypes'] .= 's';
                                    }

                                } else {
                                    $this->replace['primaryKey'] = $column['Field'];
                                }

                                if (strtolower($column['Null']) == 'no') {
                                    $this->replace['classConstantRequiredFields'] .= "'{$column['Field']}', ";
                                }

                        }

                        if ($this->replace['createMethodValidationCriteria'] == '') {
                            $this->replace['createMethodValidationCriteria'] = '                            TRUE';
                        }

                    // REMOVE UNNEEDED CHARACTERS FROM END OF VARIABLES

                        $this->replace['classVariables']                 = rtrim($this->replace['classVariables'], "\n");
                        $this->replace['classConstantDataTypes']         = rtrim($this->replace['classConstantDataTypes'], ",\n");
                        $this->replace['classConstantRequiredFields']    = rtrim($this->replace['classConstantRequiredFields'], ', ');
                        $this->replace['getters']                        = rtrim($this->replace['getters'], "\n");
                        $this->replace['allColumnNames']                 = rtrim($this->replace['allColumnNames'], ', ');
                        $this->replace['createMethodValidationCriteria'] = rtrim($this->replace['createMethodValidationCriteria'], " &&\n");
                        $this->replace['createQueryColumnNames']         = rtrim($this->replace['createQueryColumnNames'], ', ');
                        $this->replace['createQueryColumnPlaceholders']  = rtrim($this->replace['createQueryColumnPlaceholders'], ', ');
                        //$this->replace['createMethodBindTypes']          = rtrim($this->replace['createMethodBindTypes'], ', ');
                        $this->replace['createMethodBindDataString']     = rtrim($this->replace['createMethodBindDataString'], ', ');

                    // SAVE MODEL FILES

                        $baseModel     = str_replace(BaseModelBuilder::SEARCH, $this->replace, $this->baseModel);
                        $baseModelFile = __DIR__ . "/../../models/base/{$this->replace['modelName']}Model.php";
                        $fileSaved     = file_put_contents($baseModelFile, $baseModel);

                        if ($fileSaved !== FALSE) {

                            $modelFile = __DIR__ . "/../../models/{$this->replace['modelName']}.php";

                            if (!file_exists($modelFile)) {

                                $model     = str_replace(BaseModelBuilder::SEARCH, $this->replace, $this->model);
                                $fileSaved = file_put_contents($modelFile, $model);

                            }

                        }

                    // VERIFY FILE(S) WERE SAVED AND RETURN RESULT

                        if ($fileSaved !== FALSE) {
                            $modelBuilt = TRUE;
                        }

                        return $modelBuilt;

                }

            /********************************************************************************
             * RESET REPLACE ARRAY METHOD
             * @return void
             ********************************************************************************/

                private function resetReplaceArray(): void {

                    foreach ($this->replace as $key => $value) {
                        $this->replace[$key] = '';
                    }

                }

            /********************************************************************************
             * PROMPT METHOD
             * @param string $message
             * @param boolean $displayDash
             ********************************************************************************/

                private function prompt(string $message, bool $displayDash = TRUE): void {

                    if ($displayDash) {
                        $message = '- ' . $message;
                    }

                    echo "{$message}\n";

                }

        }

        new BaseModelBuilder();

?>