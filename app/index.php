<?php

    use Manevia\DB;
    use Manevia\Utilities;

    /********************************************************************************
     * SET CACHE IN INI | SET ERRORS TO TRUE FOR DEVELOPMENT ENVIRONMENTS
     ********************************************************************************/

        ini_set('session.save_handler', getenv('SESSION_SAVE_HANDLER'));
        ini_set('session.save_path', getenv('SESSION_SAVE_PATH'));

        if (getenv('ENVIRONMENT') === 'development')
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

    /********************************************************************************
     * DEFAULT AND APPROVED PAGES (default MUST be on first level of approved pages array!)
     ********************************************************************************/

        $defaultPage   = 'home';
        $approvedPages = ['error', 'home'];

    /********************************************************************************
     * COMPOSER AUTO LOAD | INSTANTIATE REQUIRED LIBRARIES -> DB
     ********************************************************************************/

        require('vendor/autoload.php');
        DB::initialize();

    /********************************************************************************
     * ROUTING -> EXTRACT REQUESTED PAGE AND PASSED VARIABLES FROM THE URL
     ********************************************************************************/

        // INSTANTIATE VARIABLES -> PAGE | PAGE HOLDER | ARRAY HOLDER VALUES ARRAY

            $page          = '';
            $pageIsArray   = NULL;
            $arrayHolder   = $approvedPages;
            $values        = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        //  BUILD PAGE NAME

            if (!empty($values[0]))
            {
                do
                {
                    // GET EXTRACTED PAGE > SEE IF IT EXISTS IN APPROVED PAGES ARRAY AS A STRING OR ARRAY

                        $extractedPage = strtolower(array_shift($values));
                        $pageIsArray   = key_exists($extractedPage, $arrayHolder);

                        if (in_array($extractedPage, $arrayHolder) || $pageIsArray)
                        {
                            // GET PAGE NAME | IF EXTRACTED PAGE IS AN ARRAY, UPDATE ARRAY HOLDER

                                if (empty($page)) {$page .= Utilities::slugToCamel($extractedPage);}
                                else {$page .= Utilities::slugToCamel($extractedPage, TRUE);}

                                if (!empty($arrayHolder[$extractedPage]) && is_array($arrayHolder[$extractedPage])) {$arrayHolder = $arrayHolder[$extractedPage];}
                        }
                        else (array_unshift($values, $extractedPage));
                }
                while ($pageIsArray);
            }
            else {$page = $defaultPage;}

        // IF NO VALID PAGE EXISTS, REDIRECT WITH ERROR | IF NO VALUES WERE PASSED, MAKE SURE IT'S AN EMPTY ARRAY

            if (empty($page))
            {
                header('Location: /error/404');
                exit;
            }

            if (!is_array($values)) {$values = [];}

    /********************************************************************************
     * CONTROLLER -> INSTANTIATE NAME | LOAD | PASS DATA TO VIEW FOR RENDERING
     ********************************************************************************/

        $controller = ucfirst($page) . 'Controller';
        require("controllers/{$controller}.php");
        new $controller($values);

?>
