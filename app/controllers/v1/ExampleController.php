<?php

    use Manevia\Controllers\v1Controller;

    /********************************************************************************
     * EXAMPLE CONTROLLER
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class ExampleController extends v1Controller {

            /********************************************************************************
             * CLASS VARIABLES
             * @var array MESSAGES
             ********************************************************************************/

                const MESSAGES = [
                    'get'    => 'get() method was called',
                    'post'   => 'post() method was called',
                    'put'    => 'put() method was called',
                    'delete' => 'delete() method was called'
                ];

            /*
                -------------------------       MAGIC METHODS       -------------------------
             */

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param bool $authorizationRequired
             * @param array $urlValues
             ********************************************************************************/

                public function __construct(bool $authorizationRequired, array $urlValues) {
                    parent::__construct($authorizationRequired, $urlValues);
                }

            /********************************************************************************
             * DESTRUCT METHOD
             ********************************************************************************/

                public function __destruct() {
                    parent::__destruct();
                }

            /*
                ---------------------------- REQUEST TYPE METHODS ---------------------------
             */

            /********************************************************************************
             * GET METHOD
             * @retrun void
             ********************************************************************************/

                public function get(): void {
                    $this->setResponse(['whatHappened' => self::MESSAGES['get']]);
                }

            /********************************************************************************
             * POST METHOD
             * @retrun void
             ********************************************************************************/

                public function post(): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['post']]);
                }

            /********************************************************************************
             * PUT METHOD
             * @retrun void
             ********************************************************************************/

                public function put(): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['put']]);
                }

            /********************************************************************************
             * DELETE METHOD
             * @retrun void
             ********************************************************************************/

                public function delete(): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['delete']]);
                }

        }

?>
