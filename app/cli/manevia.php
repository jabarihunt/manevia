#!/usr/bin/env php
<?php

    // SET VALID COMMANDS CONSTANT | IMPORT .env VALUES

        const VALID_COMMANDS = ['backup-db', 'deploy', 'help', 'build-models'];

        require('/var/www/html/vendor/autoload.php');
        $dotenv = new \Dotenv\Dotenv('/var/www/html');
        $dotenv->load();

    // MAKE SURE A VALID COMMAND WAS PASSED

        if (!empty($argv[1]) && in_array($argv[1], VALID_COMMANDS))
        {
            // EXECUTE COMMANDS

                switch ($argv[1])
                {
                    case 'backup-db'   : shell_exec("mysqldump --user='{$_ENV['DATABASE_USER']}' --password='{$_ENV['DATABASE_PASSWORD']}' {$_ENV['DATABASE_NAME']} > {$_ENV['WEB_ROOT']}/backup/database/backup_db-" . time() . '.sql'); break;
                    case 'deploy'      : break; //echo shell_exec('/var/www/html/cli/build.sh'); break;
                    case 'build-models': buildModels(); break;
                    case 'help'        : displayCommands(); break;
                }
        }
        else {displayCommands();}

    // BUILD MODELS FUNCTION

        function buildModels()
        {
            require('/var/www/html/cli/model_builder_docs/BaseModelBuilder.php');
            shell_exec('php /usr/local/bin/composer install --optimize-autoloader');
        }

    // DISPLAY COMMANDS FUNCTION

        function displayCommands()
        {
            $format = "%s \r\n";

            // COMMAND HEADER
            echo "\r\n";
            echo sprintf($format, "      --------------------------");
            echo sprintf($format, "      - Valid manevia commands -");
            echo sprintf($format, "      --------------------------\r\n");

            $format = "%20s   %s\r\n";

            // backup_db
            echo sprintf($format, 'backup-db:', "Does a MySQL dump of the database (requires DB credentials).");
            echo sprintf($format, '', "Backups are located in the /backup directory.\r\n");

            // deploy
            echo sprintf($format, 'deploy:', "Experimental, explore with your own risk!\r\n");

            // models
            echo sprintf($format, 'build-models:', "Updates base models from database.");
            echo sprintf($format, '', "NOTE: This will not overwrite changes to you custom methods!\r\n");

            // help
            echo sprintf($format, 'help:', "Displays all valid manevia commands.\r\n");
        }

?>