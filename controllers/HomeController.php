<?php

    use Manevia\Controller;
    use Manevia\Widget;

    /********************************************************************************
     * HOME CONTROLLER
     * PHP version 5.6+
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class HomeController extends Controller
        {
            /********************************************************************************
             * CLASS VARIABLES
             * @var string $helloWorldMessage
             ********************************************************************************/

                public $helloWorldMessage;

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param array $values
             ********************************************************************************/

                public function __construct(Array $values)
                {
                    parent::__construct();
                    $this->helloWorldMessage = 'Hello World, I am Manevia!';
                    $this->loadTemplate('home');
                }
        }

?>
