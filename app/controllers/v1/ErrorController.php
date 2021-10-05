<?php

    use Manevia\Controllers\v1Controller;
    use Manevia\Utilities;

    /********************************************************************************
     * ERROR CONTROLLER
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class ErrorController extends v1Controller {

            /********************************************************************************
             * CLASS VARIABLES
             ********************************************************************************/


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

                    // SET HTTP CODE | SEND RESPONSE

                        if (
                            !empty($this->requestUrlValues[0]) &&
                            !empty(self::HTTP_ERRORS[$this->requestUrlValues[0]])
                        ) {
                            $httpCode = $this->requestUrlValues[0];
                        } else {
                            $httpCode = '404';
                        }

                        $this->setResponse(NULL, $httpCode, self::HTTP_ERRORS[$httpCode]);

                }

        }

?>
