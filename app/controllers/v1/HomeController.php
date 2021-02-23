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

                private string $helloWorldMessage;

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param bool $authorizationRequired
             * @param array $values
             ********************************************************************************/

                public function __construct(bool $authorizationRequired, array $values) {

                    parent::__construct($authorizationRequired);
                    $this->helloWorldMessage = 'Hello World, I am Manevia!';
                    $this->setResponse(['message' => $this->helloWorldMessage]);

                }

            /********************************************************************************
             * DESTRUCT METHOD
             ********************************************************************************/

                public function __destruct() {
                    parent::__destruct();
                }

        }

?>
