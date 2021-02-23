<?php namespace Manevia;

    class Controller {

        /********************************************************************************
         * CLASS VARIABLES
         * @var string $response
         ********************************************************************************/

            protected string $response;

        /********************************************************************************
         * CONSTRUCT METHOD
         * @param bool $authorizationRequired
         * @param array $values
         * @param bool $useCors
         ********************************************************************************/

            public function __construct(bool $authorizationRequired, array $values, bool $useCors = TRUE) {

                if (!$authorizationRequired || $this->requestIsAuthorized()) {

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

                    // CALL REQUEST METHOD

                        $requestMethod = strtolower(trim($_SERVER['REQUEST_METHOD']));

                        if (method_exists($this, $requestMethod)) {
                            $this->$requestMethod($values);
                        }


                } else {

                    // SEND UNAUTHORIZED

                        $this->setResponse(['authorized' => false], 401);

                }
            }

        /********************************************************************************
         * DESTRUCT METHOD
         ********************************************************************************/

            public function __destruct() {

                // HANDLE EMPTY RESPONSE

                    if (empty($this->response)) {
                        $this->setResponse([], 500, 'Internal Server Error');
                    }

                    echo $this->response;

            }

        /********************************************************************************
         * REQUEST IS AUTHORIZED METHOD
         * @return bool
         ********************************************************************************/

            private function requestIsAuthorized(): bool {

                // SET INITIAL VARIABLES

                    $isAuthorized = FALSE;
                    $headers      = apache_request_headers();

                // CHECK AUTHORIZATION

                    if (!empty($headers['Authorization'])) {
                        /* TODO: DO REQUEST AUTHORIZATION HERE */
                    }

                // RETURN RESULT
return true;
                    return $isAuthorized;

            }

        /********************************************************************************
         * GET JSON DATA METHOD
         * @return array|null
         ********************************************************************************/

            protected function getJSONData(): array|null {

                $data = file_get_contents("php://input");
                return !empty($data) ? json_decode($data, TRUE) : NULL;

            }

        /********************************************************************************
         * SET RESPONSE MESSAGE METHOD
         * BASED ON JSEND: https://github.com/omniti-labs/jsend
         * @param array $data
         * @param int $htmlCode
         * @param string|null $errorMessage
         * @return void
         ********************************************************************************/

            protected function setResponse(array $data = [], int $htmlCode = 200, string $errorMessage = NULL): void {

                // BUILD RESPONSE

                    $response = [];

                    if ($htmlCode === 200 && !empty($data)) {

                        header("HTTP/1.1 200 OK");

                        $response = [
                            'status' => 'success',
                            'data'   => $data
                        ];

                    } else {

                        header("HTTP/1.1 {$htmlCode} {$errorMessage}");
                        $response['status'] = ($htmlCode >= 500) ? 'error' : 'fail';

                        if ($response['status'] === 'error') {

                            $response['code']    = $htmlCode;
                            $response['message'] = $errorMessage;

                        } else {

                            if (empty($data)) {

                                $response['data'] = [
                                    'code' => $htmlCode,
                                    'message' => $errorMessage
                                ];

                            }

                        }

                    }

                // SET CLASS RESPONSE VARIABLE AS JSON STRING

                    $this->response = json_encode($response);

            }

    }

?>