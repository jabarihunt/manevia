<?php namespace Manevia;

	/**
	* Password Handler
	* Version: 2.0
	* Author: Jabari J. Hunt <jabari@jabari.net>
	*
	* This class was developed to handle passwords for the application calling it.
	* There are three functions. createPassword() creates and returns a new
	* salted & hashed password.  comparePasswords() compares a given password to
	* another (that must be created by this class) to see if they match.
    * isValid() checks a user's password to see if it meets some basic password
    * validation rules (rules can be easily altered for your use).
	*
	* There is no "recovery" method, as passwords should only be hashed. This
	* prevents any possibility of password encryption being cracked, as well as plain
	* text passwords being exposed.
	*
	* To prevent rainbow type attacks, passwords have a random salt. The same password
	* will result in two different hashes every single time. Basically, there should
	* be no rhyme or reason to returned hashed passwords.
	*
	* This class assumes you have already made sure the password has the minimum
	* number of characters you require. It will die if an empty password is entered!
	* To be certain, use the isValid() method BEFORE calling the create() method.
	*
	* The returned hash will be 68 chars long, so set your DB table column accordingly!
	*
	*/

    final class Password
    {
        //////////////////////////////////////////////
        // CREATE PASSWORD METHOD
        //////////////////////////////////////////////

            public static function create($password)
            {
                // MAKE SURE PASSWORD ISN'T EMPTY

                    if (!empty($password))
                    {
                        // CREATE 32 CHAR SALT AND BREAK UP INTO TWO PARTS
                        // BREAK UP MD5'D and SHA1'D PASSWORD INTO TWO PARTS

                            $salt  = md5(uniqid(mt_rand(), true));
                            $salt1 = substr($salt, 16, 16);
                            $salt2 = substr($salt, 0, 16);

                            $password1 = substr(md5($password), 0, 16);
                            $password2 = substr(sha1($password), 20, 20);

                        // RETURN PARTS MESHED TOGETHER -> SALT1 + PASSWORD1 + SALT2 + PASSWORD2

                            return $salt1 . $password2 . $salt2 . $password1;
                    }
                    else {die('<b style="color:#F00;">CAN NOT CREATE HASHED PASSWORDS WITH AN EMPTY STRING!!!</b>');}
            }

        //////////////////////////////////////////////
        // COMPARE PASSWORDS METHOD
        //////////////////////////////////////////////

            public static function compare($enteredPassword, $storedPassword)
            {
                // MD5 & SHA1 ENTERED PASSWORD AND EXTRACT HASHED PASSWORD
                // COMPARE THE TWO AND RETURN THE RESULT

                    $enteredPassword = substr(md5($enteredPassword), 0, 16) . substr(sha1($enteredPassword), 20, 20);
                    $hashedPassword  = substr($storedPassword, 52, 16) . substr($storedPassword, 16, 20);

                    if ($enteredPassword == $hashedPassword) {return TRUE;}
                    else {return FALSE;}
            }

        //////////////////////////////////////////////
        // VALIDATES PASSWORD RULES
        // -> NOT EMPTY
        // -> AT LEAST EIGHT CHARS
        // -> CONTAINS ONE UPPERCASE CHAR
        // -> CONTAINS ONE LOWERCASE CHAR
        // -> CONTAINS ONE NUMBER
        // -> DOES NOT CONTAIN USERNAME (IF PASSED)
        //////////////////////////////////////////////

            public static function isValid($password, $username = NULL)
            {
                // SET INITIAL RETURN VARIABLE | TEST PASSWORD VALIDITY | RETURN RESULT

                    $passwordIsValid = FALSE;

                    if
                    (
                        !empty($password) &&                    // NOT EMPTY
                        strlen($password) >= 8 &&               // AT LEAST EIGHT CHARS
                        preg_match('/[A-Z]/', $password) &&     // CONTAINS ONE UPPERCASE CHAR
                        preg_match('/[a-z]/', $password) &&     // CONTAINS ONE LOWERCASE CHAR
                        preg_match('/[0-9]/', $password)        // CONTAINS ONE NUMBER
                    )
                    {
                        // IF USERNAME WAS PASSED, MAKE SURE IT ISN'T IN THE PASSWORD

                            if ($username === NULL) {$passwordIsValid = TRUE;}
                            else if (strpos(strtolower($password), strtolower($username)) === FALSE) {$passwordIsValid = TRUE;}
                    }

                    return $passwordIsValid;
            }
    }

?>