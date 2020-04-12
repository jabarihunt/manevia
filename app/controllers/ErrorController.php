<?php

    use Manevia\Controller;

    /********************************************************************************
     * ERROR CONTROLLER
     * PHP version 7.1+
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class ErrorController extends Controller {

            /********************************************************************************
             * CLASS VARIABLES
             * @var array ERRORS
             * @var string $errorCode
             * @var string $errorMessage
             ********************************************************************************/

				const ERRORS = [
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

                public $errorCode;
                public $errorMessage;

            /********************************************************************************
             * CONSTRUCT METHOD
             * @param array $values
             ********************************************************************************/

                public function __construct(array $values) {

                    parent::__construct();

                    // MAKE SURE A VALID ERROR CODE WAS PASSED. IF NOT, REDIRECT TO 404

                        if (!empty($values) && array_key_exists($values[0], self::ERRORS)) {
                            $this->errorCode = $values[0];
                        }
                        else {

                            header("HTTP/1.1 404 Not Found");
                            header('Location: /error/404');
                            exit;

                        }

                    // SET -> ERROR MESSAGE | HEADER HTTP CODE & MESSAGE | PAGE TITLE | TEMPLATE

                        $this->errorMessage = self::ERRORS[$this->errorCode];
                        header("HTTP/1.1 {$this->errorCode} {$this->errorMessage}");
                        $this->setPageTitle("{$this->errorCode} - {$this->errorMessage}");
                        $this->loadTemplate('error');

                }
        }

?>
