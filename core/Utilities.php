<?php namespace Manevia;

    /********************************************************************************
     * UTILITIES CLASS
     * PHP version 7.1
     * @author Jabari J. Hunt <jabari@jabari.net>
     ********************************************************************************/

        final class Utilities
        {
            /********************************************************************************
             * IS INTEGER METHOD
             * @param mixed $value
             * @return boolean
             ********************************************************************************/

                public static function isInteger($value)
                {
                    return(ctype_digit(strval($value)));
                }

            /********************************************************************************
             * ARRAY TO CSV METHOD
             * @param array $data
             * @return string
             ********************************************************************************/

                public static function arrayToCsv(Array $data)
                {
                    return count($data) > 0 ?  implode(',', $data) : '';
                }

            /********************************************************************************
             * CSV TO ARRAY METHOD
             * @param array $data
             * @return array
             ********************************************************************************/

                public static function csvToArray($csv)
                {
                    return is_string(trim($csv)) ? explode(',', $csv) : [];
                }

            /********************************************************************************
             * PLURAL TO SINGULAR
             * @param string $word Word to be made singular
             * @return boolean
             ********************************************************************************/

                public static function pluralToSingular($word)
                {
                    if (!empty($word) && is_string($word))
                    {
                        // SET INITIAL VARIABLES

                            $firstLetter = $word[0];

                            $specialCaseWords =
                            [

                            ];

                            $wordEndings =
                            [
                                'ies' => 'y',
                                'oes' => 'oe',
                                'ves' => 'f',
                                'xes' => 'x',
                                'os'  => 'o',
                                's'   => ''
                            ];

                        // HANDLE WORD TYPE

                            if (array_key_exists(strtolower($word), $specialCaseWords)) {$word = $specialCaseWords[$word];}
                            else
                            {
                                // LOOP THROUGH WORD ENDINGS -> BUILD WORD ON MATCH

                                    foreach($wordEndings as $ending => $replacement)
                                    {
                                        if (substr($word, (strlen($ending) * -1)) == $ending)
                                        {
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
             * @param boolean $firstLetterUpper Determines if the first letter should be upper case
             * @return string
             ********************************************************************************/

                public static function snakeToCamel($value, $firstLetterUpper = FALSE)
                {
                    if (is_string($value))
                    {
                        $value = str_replace('_', ' ', strtolower($value));
                        $value = str_replace(' ', '', ucwords($value));

                        if (!$firstLetterUpper) {$value = lcfirst($value);}
                    }

                    return $value;
                }

            /********************************************************************************
             * SLUG TO CAMEL CASE METHOD
             * @param string $value String value to be converted to camel case.
             * @param boolean $firstLetterUpper Determines if the first letter should be upper case
             * @return string
             ********************************************************************************/

                public static function slugToCamel($value, $firstLetterUpper = FALSE)
                {
                    if (is_string($value))
                    {
                        $value = str_replace('-', ' ', strtolower($value));
                        $value = str_replace(' ', '', ucwords($value));

                        if (!$firstLetterUpper) {$value = lcfirst($value);}
                    }

                    return $value;
                }

            /********************************************************************************
             * VALIDATE EMAIL METHOD
             * @param string $email
             * @return boolean
             ********************************************************************************/

                public static function validateEmail($email)
                {
                    // SET INITIAL RETURN VARIABLE

                        $emailIsValid = FALSE;

                    // MAKE SURE AN EMPTY STRING WASN'T PASSED

                        if (!empty($email))
                        {
                            // GET EMAIL PARTS

                                $domain = ltrim(stristr($email, '@'), '@');
                                $user   = stristr($email, '@', TRUE);

                            // VALIDATE EMAIL ADDRESS

                                if
                                (
                                    !empty($user) &&
                                    !empty($domain) &&
                                    checkdnsrr($domain)
                                )
                                {$emailIsValid = TRUE;}
                        }

                    // RETURN RESULT

                        return $emailIsValid;
                }
        }

?>
