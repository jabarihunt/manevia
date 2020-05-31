<?php namespace Manevia;

    use Mustache_Engine;
    use Mustache_Loader_FilesystemLoader;

    class Controller {

        /********************************************************************************
         * CLASS VARIABLES
         * @var array $i18n Internationalization array
         * @var array $openGraph Open Graph array
         ********************************************************************************/

            public $i18n;
            public $openGraph;

        /********************************************************************************
         * CONSTRUCT METHOD
         * @param bool $authorizationRequired
         ********************************************************************************/

            public function __construct(bool $authorizationRequired = FALSE) {

                // CHECK IF REQUEST REQUIRED AUTHORIZATION -> RESPOND ACCORDINGLY

                    if ($authorizationRequired && !$this->requestIsAuthorized()) {
                        // DO AUTHORIZATION
                    } else {

                        // SET -> LOCALE | INTERNATIONALIZATION | MUSTACHE AUTOLOADER

                            $this->setOpenGraph();
                            $this->setLocale();
                            $this->setI18n();

                    }

            }

        /********************************************************************************
         * LOAD TEMPLATE METHOD
         * @param string $template
         * @param array $templateValues
         * @return void
         ********************************************************************************/

            protected function loadTemplate(string $template, array $templateValues = NULL): void {

                $mustache = new Mustache_Engine
                ([
                    'loader'           => new Mustache_Loader_FilesystemLoader('views'),
                    'partials_loader'  => new Mustache_Loader_FilesystemLoader('views/partials'),
                    'cache'            => 'cache/mustache',
                    'strict_callables' => true
                ]);

                if ($templateValues === NULL) {
                    $templateValues = $this;
                }

                echo $mustache->render("{$template}.mustache", $templateValues);
            }

        /********************************************************************************
         * CHECK AUTHORIZATION METHOD
         * @return bool
         ********************************************************************************/

            private function requestIsAuthorized(): bool {

                // DO CHECK AUTHORIZATION
                return TRUE;

            }

        /********************************************************************************
         * SET GLOBAL INTERNATIONALIZATION METHOD
         * @return void
         ********************************************************************************/

            private function setI18n(): void {

                /* GETTEXT EXAMPLE

                    $localeDetails = localeconv();

                    $this->i18n['Home']          = _('Home');
                    $this->i18n['Sorry']         = _('Sorry');

                    if ($localeDetails['p_cs_precedes'])
                    {
                        $this->i18n['CurrencySymbolPrefix'] = trim($localeDetails['currency_symbol']);
                        $this->i18n['CurrencySymbolSuffix'] = NULL;
                    }
                    else
                    {
                        $this->i18n['CurrencySymbolPrefix'] = NULL;
                        $this->i18n['CurrencySymbolSuffix'] = trim($localeDetails['currency_symbol']);
                    }

                    if (empty($this->user))
                    {
                        $this->i18n['FormEmailUsername']         = _('Username Or Email');
                        $this->i18n['FormEmailUsernameRequired'] = _('Username or Email is required!');
                        $this->i18n['FormForgotPassword']        = _('forgot password');
                        $this->i18n['FormLetMeIn']               = _('Let Me In!');
                        $this->i18n['FormPasswordAgainRequired'] = _('Passwords Must Match');
                        $this->i18n['FormPaypalEmail']           = _('PayPal Email');
                        $this->i18n['FormPaypalEmailRequired']   = _('PayPal Email is required!');
                        $this->i18n['FormUsernameRequired']      = _('Username is Required!');
                        $this->i18n['FormWhyPaypal']             = _('Why do you need my PayPal email?');
                    }
                */

            }

        /********************************************************************************
         * SET LOCALE METHOD
         * @return void
         ********************************************************************************/

            private function setLocale(): void {

                // SET INITIAL VARIABLES

                    $folder   = $_SERVER['DOCUMENT_ROOT'] . '/backup/locale';
                    $domain   = 'messages';
                    $encoding = 'UTF-8';
                    $locale   = 'en_US';

                // SET OPEN GRAPH LOCALE

                    $this->openGraph['locale'] = $locale;

                // SET I18N ENVIRONMENT VARIABLES

                    putenv("LC_ALL={$locale}.{$encoding}");
                    setlocale(LC_ALL, "{$locale}.{$encoding}");

                // RUN GETTEXT DOMAIN METHODS

                    bindtextdomain($domain, $folder);
                    bind_textdomain_codeset($domain, $encoding);
                    textdomain($domain);

            }

        /********************************************************************************
         * SET OPEN GRAPH METHOD
         * @return void
         ********************************************************************************/

            private function setOpenGraph(): void {

                    $this->openGraph = [

                        // GENERAL

                            'type'        => 'website',
                            'title'       => 'Manevia Framework',
                            'url'         => "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
                            'image'       => "https://{$_SERVER['HTTP_HOST']}{/images/logo.png", // TODO: Manevia Logo?
                            'description' => 'A lightweight PHP framework for developers who love SQL.',

                        // TWITTER

                            'twitterCard' => 'summary_large_image',
                            'twitterSite' => '@Manevia',
                            'twitterCreator' => '@Manevia'

                    ];

            }

        /********************************************************************************
         * SET PAGE TITLE METHOD
         * @param string $pageTitle
         * @return void
         ********************************************************************************/

            protected function setPageTitle(string $pageTitle): void {

                if (strlen($pageTitle) > 0) {
                    $this->openGraph['title'] .= " | {$pageTitle}";
                }

            }

    }

?>