<?php namespace Manevia\Controllers\v1;

    class Controller {

        /********************************************************************************
         * CLASS VARIABLES
         * @var string $requestBody
         * @var array $requestUrlValues
         * @var string $response
         * @var array ERRORS
         ********************************************************************************/

            protected string $requestBody;
            protected array $requestUrlValues;
            private array|false $requestHeaders;
            protected string $response;

            const HTTP_ERRORS = [
                '400' => 'Bad Request',
                '401' => 'Unauthorized',
                '402' => 'Payment Required',
                '403' => 'Forbidden',
                '404' => 'Not Found',
                '405' => 'Method Not Allowed',
                '406' => 'Not Acceptable',
                '407' => 'Proxy Authentication Required',
                '408' => 'Request Timeout',
                '409' => 'Conflict',
                '410' => 'Gone',
                '411' => 'Length Required',
                '412' => 'Precondition Failed',
                '413' => 'Payload Too Large',
                '414' => 'Request-URI Too Long',
                '415' => 'Unsupported Media Type',
                '416' => 'Requested Range Not Satisfiable',
                '417' => 'Expectation Failed',
                '418' => 'I\'m a teapot',
                '421' => 'Misdirected Request',
                '422' => 'Unprocessable Entity',
                '423' => 'Locked',
                '424' => 'Failed Dependency',
                '426' => 'Upgrade Required',
                '428' => 'Precondition Required',
                '429' => 'Too Many Requests',
                '431' => 'Request Header Fields Too Large',
                '444' => 'Connection Closed Without Response',
                '451' => 'Unavailable For Legal Reasons',
                '499' => 'Client Closed Request',
                '500' => 'Internal Server Error',
                '501' => 'Not Implemented',
                '502' => 'Bad Gateway',
                '503' => 'Service Unavailable',
                '504' => 'Gateway Timeout',
                '505' => 'HTTP Version Not Supported',
                '506' => 'Variant Also Negotiates',
                '507' => 'Insufficient Storage',
                '508' => 'Loop Detected',
                '510' => 'Not Extended',
                '511' => 'Network Authentication Required',
                '599' => 'Network Connect Timeout Error'
            ];

        /********************************************************************************
         * CONSTRUCT METHOD
         * @param bool $authorizationRequired
         * @param array $requestUrlValues
         * @param bool $useCors
         ********************************************************************************/

            public function __construct(bool $authorizationRequired, array $requestUrlValues, bool $useCors = TRUE) {

                // GET - URL VALUES & REQUEST HEADERS | SET CONTENT TYPE HEADER

                    $this->requestUrlValues = $requestUrlValues;
                    $this->requestHeaders   = apache_request_headers();

                    header('Content-Type: application/json');

                // SET CORS HEADERS -> HANDLE PREFLIGHT REQUESTS

                    if ($useCors) {

                        header('Access-Control-Allow-Origin: *');

                        if ($this->isCorsPreflightRequest()) {
                            $this->response = '';
                            header('Connection: Keep-Alive');
                            header('Access-Control-Allow-Methods: *');
                            header('Access-Control-Max-Age: 604800');
                            header('Access-Control-Allow-Headers: Authorization');
                            header("HTTP/1.1 200 OK");
                            return;
                        }

                    }

                // CHECK AUTHORIZATION

                    if (
                        ($authorizationRequired && $this->requestIsAuthorized()) ||
                        !$authorizationRequired
                    ) {

                        // CALL REQUEST METHOD

                            $requestMethod = strtolower(trim($_SERVER['REQUEST_METHOD']));

                            if (method_exists($this, $requestMethod)) {

                                // GET BODY | CALL REQUEST METHOD

                                    $this->requestBody = file_get_contents('php://input');
                                    $this->$requestMethod();

                            } else {
                                $this->setResponse([], 405);
                            }

                    } else {
                        $this->setResponse(['authorized' => false], 401);
                    }
            }

        /********************************************************************************
         * DESTRUCT METHOD
         ********************************************************************************/

            public function __destruct() {

                // HANDLE EMPTY RESPONSE

                    if (empty($this->response)  && !$this->isCorsPreflightRequest()) {
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

                    return $isAuthorized;

            }

        /********************************************************************************
         * GET JSON DATA METHOD
         * @return array|null
         ********************************************************************************/

            protected function getJSONData(): array|null {
                return !empty($json) ? json_decode($json, TRUE) : NULL;
            }

        /********************************************************************************
         * SET RESPONSE MESSAGE METHOD
         * BASED ON JSEND: https://github.com/omniti-labs/jsend
         * @param array $data
         * @param int $httpCode
         * @param string|null $errorMessage
         * @return void
         ********************************************************************************/

            protected function setResponse(array|null $data = [], int $httpCode = 200, string $errorMessage = NULL): void {

                // BUILD RESPONSE

                    $response = [];

                    if ($httpCode === 200) {

                        header("HTTP/1.1 200 OK");

                        $response = [
                            'status' => 'success',
                            'data'   => $data ?? []
                        ];

                    } else {
                        header("HTTP/1.1 {$httpCode} {$errorMessage}");
                        $response['status']  = ($httpCode >= 500) ? 'error' : 'fail';
                        $response['code']    = $httpCode;
                        $response['message'] = self::HTTP_ERRORS[strval($httpCode)];
                    }

                // SET CLASS RESPONSE VARIABLE AS JSON STRING

                    $this->response = json_encode($response);

            }
            
        /********************************************************************************
         * IS CORS REQUEST METHOD
         * @return bool
         ********************************************************************************/

            private function isCorsPreflightRequest(): bool {

                return (
                    is_array($this->requestHeaders) &&
                    $_SERVER['REQUEST_METHOD'] === 'OPTIONS' &&
                    (
                        array_key_exists('Access-Control-Request-Method', $this->requestHeaders) ||
                        array_key_exists('access-control-request-method', $this->requestHeaders)
                    )
                );

            }

    }

?>
