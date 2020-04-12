<?php

    /********************************************************************************
     * DEFAULT AND VALID ENDPOINTS (default MUST be on first level of approved endpoints array!)
     ********************************************************************************/

        const ENDPOINTS = [
            'default' => 'home',
            'valid'   => [
                'error',
                'home'
            ]
        ];

    /********************************************************************************
     * COMPOSER AUTO LOAD -> LOAD REQUIRED CLASSES
     ********************************************************************************/

        require('vendor/autoload.php');
        use Manevia\Utilities;

    /********************************************************************************
     * CONFIGURE AND START SESSIONS
     ********************************************************************************/

        $useSessions = (bool) getenv('SESSION_ENABLED');

        if ($useSessions) {

            ini_set('session.save_handler', getenv('SESSION_SAVE_HANDLER'));
            ini_set('session.save_path', getenv('SESSION_SAVE_PATH'));
            ini_set('session.gc_probability', 1);

            session_start();

        }

    /********************************************************************************
     * DISPLAY ERRORS IN DEVELOPMENT ENVIRONMENT
     ********************************************************************************/

        if (getenv('ENVIRONMENT') === 'development') {

            error_reporting(E_ALL);
            ini_set('display_errors', 1);

        }

    /********************************************************************************
     * ROUTING -> EXTRACT REQUESTED ENDPOINT AND PASSED VARIABLES FROM THE URL
     ********************************************************************************/

        // INSTANTIATE VARIABLES -> ENDPOINT | ENDPOINT HOLDER | ARRAY HOLDER VALUES ARRAY

            $endpoint        = '';
            $endpointIsArray = NULL;
            $arrayHolder     = ENDPOINTS['valid'];
            $values          = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        //  BUILD ENDPOINT NAME

            if (!empty($values[0])) {

                do {

                    // GET EXTRACTED ENDPOINT > SEE IF IT EXISTS IN VALID ENDPOINTS ARRAY AS A STRING OR ARRAY

                        $extractedEndpoint = strtolower(array_shift($values));
                        $endpointIsArray   = key_exists($extractedEndpoint, $arrayHolder);

                        if (in_array($extractedEndpoint, $arrayHolder) || $endpointIsArray) {

                            // GET ENDPOINT NAME | IF EXTRACTED ENDPOINT IS AN ARRAY, UPDATE ARRAY HOLDER

                                if (empty($endpoint)) {
                                    $endpoint .= Utilities::slugToCamel($extractedEndpoint);
                                } else {
                                    $endpoint .= Utilities::slugToCamel($extractedEndpoint, TRUE);
                                }

                                if (!empty($arrayHolder[$extractedEndpoint]) && is_array($arrayHolder[$extractedEndpoint])) {
                                    $arrayHolder = $arrayHolder[$extractedEndpoint];
                                }

                        } else {
                            array_unshift($values, $extractedEndpoint);
                        }

                } while ($endpointIsArray);

            } else {
                $endpoint = ENDPOINTS['default'];
            }

        // IF NO VALID ENDPOINT EXISTS, REDIRECT WITH ERROR | IF NO VALUES WERE PASSED, MAKE SURE IT'S AN EMPTY ARRAY

            if (empty($endpoint)) {

                header('Location: /error/404');
                exit;

            }

            if (!is_array($values)) {
                $values = [];
            }

    /********************************************************************************
     * CONTROLLER -> INSTANTIATE NAME | LOAD | PASS DATA TO VIEW FOR RENDERING
     ********************************************************************************/

        $controller = ucfirst($endpoint) . 'Controller';
        require("controllers/{$controller}.php");
        new $controller($values);

    /********************************************************************************
     * CONTROLLER -> INSTANTIATE NAME | LOAD | PASS DATA TO VIEW FOR RENDERING
     ********************************************************************************/

        if ($useSessions) {
            session_write_close();
        }

?>