<?php namespace Manevia;

    /**
     *	Email Helper Class
     *	AUTHOR: Jabari J. Hunt
     *	E-MAIL: jabari@shuttertrade.com
     *	   PHP: 5
     *
     *  This class utilizes PHPMailer to handle all outgoing emails.  There is a private static
     *  method called "initialize()" that must be called by all other methods that attempt to
     *  send an email.  The initialize function will return a PHPMailer object that can be used
     *  to add remaining message details (to, subject, etc).
     *
     **/

    ////////////////////////////////////////////////////////////////
    // REQUIRED LIBRARIES  | CLASS DECLARATION
    ////////////////////////////////////////////////////////////////

        require_once($_SERVER['DOCUMENT_ROOT'] . '/vendors/phpmailer/PHPMailerAutoload.php');

        class Email
        {
            //////////////////////////////////////////////
            //  CLASS VARIABLES
            //////////////////////////////////////////////

				private static $admin;
                private static $header;
                private static $footer;
                private static $templatePath;

                const VALIDATION_TYPE_EMAIL    = 'Email';
                const VALIDATION_TYPE_PASSWORD = 'Password';

            //////////////////////////////////////////////
            // INITIALIZER METHOD
            //////////////////////////////////////////////

                private static function initialize()
                {
                    // SET HEADER AND FOOTER FOR MESSAGES

						self::$admin        = $_ENV['EMAIL_ADMIN']);
                    	self::$templatePath = $_ENV['EMAIL_TEMPLATE_PATH']);

                        if (empty(self::$header) || empty(self::footer))
                        {
                            self::$header = file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::$templatePath . 'header.html');
                            self::$footer = file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::$templatePath . 'footer.html');
                        }

                    // INSTANTIATE PHPMAILER OBJECT | ADD DEFAULT SETTINGS | RETURN MAIL OBJECT

                    	$testMode = (boolean) $_ENV['EMAIL_TEST_MODE'];

                        $mail = new PHPMailer;
                        $mail->isHTML((boolean) $_ENV['EMAIL_IS_HTML']);
                        $mail->isSMTP();
                        $mail->SMTPAuth   = (boolean) $_ENV['EMAIL_SMTP_AUTH'];
                        $mail->SMTPSecure = $_ENV['EMAIL_SMTP_SECURE'];
                        $mail->Host       = $_ENV['EMAIL_HOST'];
                        $mail->Username   = $_ENV['EMAIL_USERNAME'];
                        $mail->Password   = $_ENV['EMAIL_PASSWORD'];
                        $mail->Port       = $_ENV['EMAIL_PORT'];
                        $mail->From       = $_ENV['EMAIL_FROM_ADDRESS'];
                        $mail->FromName   = $_ENV['EMAIL_FROM_NAME'];

                        if ($testMode)
                        {
                            $mail->SMTPDebug   = 3;       // TESTING ONLY -> Enable verbose debug output
                            $mail->Debugoutput = 'html';  // TESTING ONLY -> Enable HTML debugging output
                        }

                        return $mail;
                }

            //////////////////////////////////////////////
            //  SEND EMAIL METHODS
            //////////////////////////////////////////////

                public static function fromContactForm($name, $fromAddress, $subject, $message)
                {
                    // SET INITIAL VARIABLES

                        $emailSent   = FALSE;
                        $name        = filter_var(trim($name), FILTER_SANITIZE_STRING);
                        $subject     = filter_var(trim($subject), FILTER_SANITIZE_STRING);
                        $message     = filter_var(trim($message), FILTER_SANITIZE_STRING);

                    // VALIDATE PASSED VARIABLES

                        if
                        (
                            !empty($name) &&
                            !empty($fromAddress) &&
                            !empty($subject) &&
                            !empty($message))
                        {
                            // INITIALIZE & SETUP EMAIL

                                $mail = self::initialize();
                                $mail->addAddress(self::$admin);
                                $mail->Subject = "SHUTTER TRADE CONTACT: {$subject}";
                                $mail->Body    = "NAME: {$name}<br/>EMAIL: {$fromAddress}<br/><br/>" . str_replace("\r\n", '<br/>', $message);

                            // SEND EMAIL

                                if($mail->send()) {$emailSent = TRUE;}
                        }

                    // RETURN RESULT

                        return $emailSent;
                }

                public static function receivedMessageNotification($toUserId, $fromUserId, $fromUserMessage)
                {
                    // SET INITIAL VARIABLES

                        $emailSent = FALSE;
                        $toUser    = User::get($toUserId);
                        $fromUser  = User::get($fromUserId);

                    // VERIFY USERS EXIST | SEND EMAIL

                        if (is_object($toUser) && is_object($fromUser) && !empty($fromUserMessage))
                        {
                            // INITIALIZE EMAIL OBJECT | ADD -> RECEIVER, SUBJECT

                                $mail = self::initialize();
                                $mail->addAddress($toUser->getEmail());
                                $mail->Subject = 'You have received a message from ' . $fromUser->getUsername();

                            // CREATE MESSAGE -> ADD TO EMAIL

                                $template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::$templatePath . 'received_message_notification.html');
                                $search   = array('[USERNAME]', '[FROM_USER]', '[FROM_USER_MESSAGE]');
                                $replace  = array($toUser->getUsername(), $fromUser->getUsername(), $fromUserMessage);

                                $mail->Body = self::$header . str_replace($search, $replace, $template) . self::$footer;

                            // SEND EMAIL

                                if($mail->send()) {$emailSent = TRUE;}
                        }

                    // RETURN RESULT

                        return $emailSent;
                }

                public static function validation($type, $to, $username, $userId, $code)
                {
                    // SET INITIAL VARIABLES

                        $emailSent = FALSE;
                        $type      = filter_var(trim($type), FILTER_SANITIZE_STRING);
                        $to        = filter_var(trim($to), FILTER_VALIDATE_EMAIL);
                        $username  = filter_var(trim($username), FILTER_SANITIZE_STRING);
                        $userId    = (int) filter_var(trim($userId), FILTER_SANITIZE_NUMBER_INT);
                        $code      = filter_var(trim($code), FILTER_SANITIZE_STRING);

                    // VALIDATE PASSED VARIABLES

                        if
                        (
                            (
                                $type == User::VALIDATION_TYPE_EMAIL ||
                                $type == User::VALIDATION_TYPE_PASSWORD
                            ) &&
                            !empty($to) &&
                            !empty($username) &&
                            is_int($userId) &&
                            !empty($code)
                        )
                        {
                            // INITIALIZE EMAIL OBJECT | ADD -> RECEIVER, SUBJECT

                                $mail = self::initialize();
                                $mail->addAddress($to);
                                $mail->Subject = ucwords("ShutterTrade.com {$type} Validation");

                            // CREATE MESSAGE -> ADD TO EMAIL

                                $template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::$templatePath . 'validation.html');
                                $search   = array('[USERNAME]', '[TYPE]', '[USERID]', '[CODE]');
                                $replace  = array($username, $type, $userId, $code);

                                $mail->Body = self::$header . str_replace($search, $replace, $template) . self::$footer;

                            // SEND EMAIL

                                if($mail->send()) {$emailSent = TRUE;}
                        }

                    // RETURN RESULT

                        return $emailSent;
                }
        }

?>
