<?php

    /********************************************************************************
     * COMPOSER AUTO LOAD -> LOAD REQUIRED CLASSES
     * DISPLAY ERRORS IN DEV ENVIRONMENT
     ********************************************************************************/

        require('vendor/autoload.php');

        if (getenv('ENVIRONMENT') === 'development') {

            error_reporting(E_ALL);
            ini_set('display_errors', 1);

        }

    /********************************************************************************
     * ROUTING -> EXTRACT REQUESTED VERSION, ENDPOINT, AND PASSED VALUES FROM THE URI
     ********************************************************************************/

        $uriValues = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        if (
            !empty($uriValues[0]) &&
            !empty($uriValues[1]) &&
            $uriValues[0][0] === 'v' &&
            is_numeric(ltrim($uriValues[0], 'v'))

        ) {

            $version  = array_shift($uriValues);
            $endpoint = array_shift($uriValues);

        } else {

            $version   = 'v' . getenv('DEFAULT_API_VERSION');
            $endpoint  = 'error';
            $uriValues = ['404'];

        }

    /********************************************************************************
     * CONTROLLER -> INSTANTIATE NAME | LOAD | PASS DATA TO VIEW FOR RENDERING
     ********************************************************************************/

        $controller = ucfirst($endpoint) . 'Controller';

        if (!@include("controllers/{$version}/{$controller}.php")) {

            $version    = 'v' . getenv('DEFAULT_API_VERSION');
            $controller = 'ErrorController';
            $uriValues  = ['404'];

            include("controllers/{$version}/{$controller}.php");

        }

        new $controller($uriValues);

?>