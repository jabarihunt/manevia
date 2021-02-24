<?php

    use Manevia\Controller;

    /********************************************************************************
     * EXAMPLE CONTROLLER
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class ExampleController extends Controller {

            /********************************************************************************
             * CLASS VARIABLES
             * @var array MESSAGES
             ********************************************************************************/

                const MESSAGES = [
                    'get'    => 'get()',
                    'post'   => 'post()',
                    'put'    => 'put()',
                    'delete' => 'delete()'
                ];

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param bool $authorizationRequired
             * @param array $values
             ********************************************************************************/

                public function __construct(bool $authorizationRequired, array $values) {
                    parent::__construct($authorizationRequired, $values);
                }

            /********************************************************************************
             * GET METHOD
             * @param array $values
             * @retrun void
             ********************************************************************************/

                public function get(array $values): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['get']]);
                }

            /********************************************************************************
             * POST METHOD
             * @param array $values
             * @retrun void
             ********************************************************************************/

                public function post(array $values): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['post']]);
                }

            /********************************************************************************
             * PUT METHOD
             * @param array $values
             * @retrun void
             ********************************************************************************/

                public function put(array $values): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['put']]);
                }

            /********************************************************************************
             * DELETE METHOD
             * @param array $values
             * @retrun void
             ********************************************************************************/

                public function delete(array $values): void {
                    $this->setResponse(['methodCalled' => self::MESSAGES['delete']]);
                }

            /********************************************************************************
             * DESTRUCT METHOD
             ********************************************************************************/

                public function __destruct() {
                    parent::__destruct();
                }

        }

?>
