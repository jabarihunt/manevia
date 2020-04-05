<?php namespace Manevia;

    class APIController {

        /********************************************************************************
         * CLASS VARIABLES
         * @var string $response
         ********************************************************************************/

            public $response;

        /********************************************************************************
         * CONSTRUCT METHOD
         * @param bool $useCors
         ********************************************************************************/

            public function __construct(bool $useCors = TRUE) {

                // SET CONTENT TYPE HEADER | SET CORS HEADERS

                    header('Content-Type: application/json');

                    if ($useCors) {

                        header('Access-Control-Allow-Origin: *');

                        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

                            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                            header('Access-Control-Max-Age: 604800');
                            header('Access-Control-Allow-Headers: Authorization');

                        }

                    }

            }

        /********************************************************************************
         * DESTRUCT METHOD
         ********************************************************************************/

            public function __destruct() {

                // HANDLE EMPTY RESPONSE | LOAD TEMPLATE

                    if (empty($this->response)) {
                        $this->setResponse(NULL, 400, 'Bad Request');
                    }

                    echo $this->response;

            }

        /********************************************************************************
         * REQUEST IS AUTHORIZED METHOD
         * @return bool
         ********************************************************************************/

            protected function requestIsAuthorized(): bool {

                // SET INITIAL VARIABLES

                    $authorized = FALSE;
                    $headers    = apache_request_headers();

                    if (!empty($headers['Authorization'])) {
                        /* DO REQUEST AUTHORIZATION HERE */
                    }

                // VERIFY DATA

                    /* DO DATA VALIDATION HERE HERE */

                // RETURN RESULT

                    return $authorized;

            }

        /********************************************************************************
         * GET JSON DATA METHOD
         * @return array
         ********************************************************************************/

            protected function getJSONData(): ?array {

                $data = file_get_contents("php://input");
                return !empty($data) ? json_decode($data, TRUE) : NULL;

            }

        /********************************************************************************
         * SET RESPONSE MESSAGE METHOD
         * @param array $data
         * @param int $htmlCode
         * @param string $errorMessage
         * @return void
         ********************************************************************************/

            protected function setResponse(array $data = NULL, int $htmlCode = 200, string $errorMessage = NULL): void {

                // INSTANTIATE RESPONSE ARRAY | BUILD RESPONSE

                    $response = [];

                    if ($htmlCode === 200 && !empty($data)) {

                        header("HTTP/1.1 200 OK");
                        $response['data'] = $data;

                    } else {

                        header("HTTP/1.1 {$htmlCode} {$errorMessage}");

                        $response['errors'] = [
                            [
                                'status'  => $htmlCode,
                                'message' => $errorMessage
                            ]
                        ];

                    }

                // SET RESPONSE

                    $this->response = json_encode($response);

            }

    }

?>