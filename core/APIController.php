<?php namespace Manevia;

    class APIController
    {
        ////////////////////////////////////////////////////////////////
        // CLASS VARIABLES
        ////////////////////////////////////////////////////////////////

            // PROTECTED VARIABLES

                private $responseArray;

            // RESPONSE CODES

                const RESPONSE_OK           = 200;
                const RESPONSE_UNAUTHORIZED = 401;
                const RESPONSE_FORBIDDEN    = 403;
                const RESPONSE_NOT_FOUND    = 404;
                const RESPONSE_SERVER_ERROR = 500;

        ////////////////////////////////////////////////////////////////
        // CONSTRUCTOR & DESTRUCTOR
        ////////////////////////////////////////////////////////////////

            public function __construct($useCoors = TRUE)
            {
                // SET COORS HEADERS

                    if ($useCoors)
                    {
                        header('Access-Control-Allow-Origin: *');

                        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
                        {
                            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                            header('Access-Control-Max-Age: 604800');
                            header('Access-Control-Allow-Headers: Authorization');
                        }
                    }
            }

            public function __destruct()
            {
                // RETURN RESPONSE

                    if (!empty($this->responseArray)) {echo json_encode($this->responseArray);}
            }

        ////////////////////////////////////////////////////////////////
        // PROTECTED METHODS
        ////////////////////////////////////////////////////////////////

            protected function requestIsAuthorized()
            {
                // SET INITIAL VARIABLES

                    $authorized = FALSE;
                    $headers    = apache_request_headers();

                    if (!empty($headers['Authorization']))
                    {
                        /* DO REQUEST AUTHORIZATION HERE */
                    }

                // VERIFY DATA

                    /* DO DATA VALIDATION HERE HERE */

                // RETURN RESULT

                    return $authorized;
            }

            protected function sendOKResponse(Array $data)
            {
                // SET RESPONSE CODE | BUILD RESPONSE ARRAY

                    http_response_code(APIController::RESPONSE_OK);
                    if (is_array($data) && count($data) > 0) {$this->responseArray = array('data' => $data);}
            }

            protected function sendErrorResponse($message, $responseCode = Controller::RESPONSE_OK)
            {
                // CREATE ARRAY OF VALID RESPONSE CODES (SHOULD BE CLASS CONSTANTS)

                    $validResponseCodes = array(Controller::RESPONSE_OK, Controller::RESPONSE_FORBIDDEN, Controller::RESPONSE_NOT_FOUND, Controller::RESPONSE_SERVER_ERROR);

                // SET RESPONSE CODE | BUILD RESPONSE ARRAY

                    if (is_int($responseCode) && !empty($message) && in_array($responseCode, $validResponseCodes))
                    {
                        http_response_code($responseCode);

                        $this->responseArray = array
                        (
                            'error' => array
                            (
                                'code'    => $responseCode,
                                'message' => $message
                            )
                        );
                    }
            }
    }

?>