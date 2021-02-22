<?php

    use Manevia\Controller;

    /********************************************************************************
     * HOME CONTROLLER
     * PHP version 7.1+
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class HomeController extends Controller {

            /********************************************************************************
             * CLASS VARIABLES
             * @var string $helloWorldMessage
             ********************************************************************************/

                public $helloWorldMessage;

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param array $values
             ********************************************************************************/

                public function __construct(array $values) {

                    parent::__construct();
                    $this->helloWorldMessage = 'Hello World, I am Manevia!';
                    $this->loadTemplate('home');

                }

            /********************************************************************************
             * DESTRUCT METHOD
             ********************************************************************************/

                public function __destruct() {
                    parent::__destruct();
                }

        }

?>
