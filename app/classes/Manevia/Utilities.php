<?php namespace Manevia;

    /********************************************************************************
     * UTILITIES CLASS
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class Utilities {

            /********************************************************************************
             * IS INTEGER METHOD
             * @param mixed $value
             * @return bool
             ********************************************************************************/

                public static function isInteger(mixed $value): bool {
                    return(ctype_digit(strval($value)));
                }

            /********************************************************************************
             * ARRAY TO CSV METHOD
             * @param array $data
             * @return string
             ********************************************************************************/

                public static function arrayToCsv(array $data): string {
                    return count($data) > 0 ? implode(',', $data) : '';
                }

            /********************************************************************************
             * CSV TO ARRAY METHOD
             * @param string $csv
             * @return array
             ********************************************************************************/

                public static function csvToArray(string $csv): array {
                    return strlen(trim($csv)) > 0 ? explode(',', $csv) : [];
                }

            /********************************************************************************
             * PLURAL TO SINGULAR
             * @param string $word Word to be made singular
             * @return string
             ********************************************************************************/

                public static function pluralToSingular(string $word): string {

                    if (strlen($word) > 0) {

                        // SET INITIAL VARIABLES

                            $firstLetter      = $word[0];
                            $specialCaseWords = [];

                            $wordEndings = [
                                'ies' => 'y',
                                'oes' => 'oe',
                                'ves' => 'f',
                                'xes' => 'x',
                                'os'  => 'o',
                                's'   => ''
                            ];

                        // HANDLE WORD TYPE

                            if (array_key_exists(strtolower($word), $specialCaseWords)) {
                                $word = $specialCaseWords[$word];
                            } else {

                                // LOOP THROUGH WORD ENDINGS -> BUILD WORD ON MATCH

                                    foreach($wordEndings as $ending => $replacement) {

                                        if (substr($word, (strlen($ending) * -1)) == $ending) {

                                            $word  = substr($word, 0, strlen($word) - strlen($ending));
                                            $word .= $replacement;
                                            break;

                                        }

                                    }

                            }

                        // REPLACE THE FIRST LETTER WITH WHATEVER THE ORIGINAL WAS

                            $word[0] = $firstLetter;

                    }

                    return $word;

                }

            /********************************************************************************
             * SNAKE CASE TO CAMEL CASE METHOD
             * @param string $value String value to be converted to camel case.
             * @param bool $firstLetterUpper Determines if the first letter should be upper case
             * @return string
             ********************************************************************************/

                public static function snakeToCamel(string $value, bool $firstLetterUpper = FALSE): string {

                    if (strlen($value) > 0) {
                        $value = str_replace('_', ' ', strtolower($value));
                        $value = str_replace(' ', '', ucwords($value));
                        $value = $firstLetterUpper ? ucfirst($value) : lcfirst($value);
                    }

                    return $value;

                }

            /********************************************************************************
             * SLUG TO CAMEL CASE METHOD
             * @param string $value String value to be converted to camel case.
             * @param bool $firstLetterUpper Determines if the first letter should be upper case
             * @return string
             ********************************************************************************/

                public static function slugToCamel(string $value, bool $firstLetterUpper = FALSE): string {

                    if (strlen($value) > 0) {
                        $value = str_replace('-', ' ', strtolower($value));
                        $value = str_replace(' ', '', ucwords($value));
                        $value = $firstLetterUpper ? ucfirst($value) : lcfirst($value);
                    }

                    return $value;

                }

            /********************************************************************************
             * CAMEL TO SNAKE
             * @param string|array $data
             * @return string|array
             ********************************************************************************/

                public static function camelToSnake(string|array $data): string|array {

                    if (is_string($data)) {
                        $data = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", $data));
                    } else {
                        foreach ($data as $key => $value) {
                            if (!Utilities::isInteger($key)) {
                                $data['key'] = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", $value));
                            }
                        }
                    }

                    return $data;

                }

            /********************************************************************************
             * ARRAY KEYS TO SNAKE
             * @param array $data
             * @return array
             ********************************************************************************/

                public static function arrayKeysToSnake(array $data): array {

                    foreach ($data as $key => $value) {
                        if (!self::isInteger($key)) {

                            $newKey        = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", $key));
                            $data[$newKey] = $value;

                            if ($key !== $newKey) {
                                unset($data[$key]);
                            }

                        }
                    }

                    return $data;

                }

            /********************************************************************************
             * SWAP MULTIPLE SPACES FOR ONE
             * @param string $string value to be converted to camel case.
             * @return string
             ********************************************************************************/

                public static function swapMultipleSpacesForOne(string $string): string {
                    return strlen($string) >= 2 ? preg_replace('!\s+!', ' ', $string) : $string;
                }

            /********************************************************************************
             * GET CLIENT IP METHOD
             * @return string
             ********************************************************************************/

                public static function getClientIP(): string {

                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    return $ip;

                }

            /********************************************************************************
             * MAKE HTTP REQUEST METHOD
             * @param string $url
             * @param string $type
             * @param array $headers
             * @param array $fields
             * @throws ErrorException
             * @return string
             ********************************************************************************/

                const HTTP_REQUEST_TYPE_GET  = 'GET';
                const HTTP_REQUEST_TYPE_POST = 'POST';

                public static function makeHttpRequest(string $url, string $type = 'POST', array $headers = [], array $fields = []): string {

                    if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {

                        // INSTANTIATE CURL REQUEST -> SET URL | SET AS POST REQUEST | SET TO RETURN INSTEAD OF ECHO

                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                            if ($type == self::HTTP_REQUEST_TYPE_POST) {
                                curl_setopt($curl, CURLOPT_POST, true);
                            }

                        // ADD FIELDS TO REQUEST

                            if (!empty($fields) && count($fields) > 0 ) {

                                // BUILD FIELD STRING

                                    $fieldsString = '';
                                    foreach ($fields as $key => $value) {$fieldsString .= $key . "=" . urlencode($value) . '&';}
                                    $fieldsString = rtrim($fieldsString, '&');

                                // ADD FIELD/POST RELATED CURL OPTIONS

                                    if ($type == self::HTTP_REQUEST_TYPE_POST) {

                                        curl_setopt($curl, CURLOPT_POST, count($fields));
                                        curl_setopt($curl, CURLOPT_POSTFIELDS, $fieldsString);

                                    }

                            }

                        // ADD HEADERS TO REQUEST

                            if (!empty($headers) && count($headers) > 0 ) {

                                // BUILD HEADERS ARRAY

                                    $headersArray = [];

                                    foreach ($headers as $key => $value) {
                                        $headersArray[] = "{$key}: {$value}";
                                    }

                                    if (!empty($fieldsString)) {
                                        $headersArray[] = 'Content-Length: ' . strlen($fieldsString);
                                    }

                                // ADD HEADER RELATED FIELD OPTIONS

                                    curl_setopt($curl, CURLOPT_HEADER, true);
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headersArray);

                            } else {
                                curl_setopt($curl, CURLOPT_HEADER, false);
                            }

                        // MAKE CURL REQUEST -> CLOSE CONNECTION | RETURN RESULT

                            $result = curl_exec($curl);

                            curl_close($curl);
                            return $result;

                    }
                    else {
                        throw new ErrorException('Invalid URL passed to Utilities:makeHttpRequest()');
                    }

                }
        }

?>
