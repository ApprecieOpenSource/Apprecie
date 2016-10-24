<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Europe/London");
setlocale(LC_ALL, 'en-UK');
(new Phalcon\Debug)->listen();

/**
 * Include the macro functions
 */
include __DIR__ . "/../app/Library/macros.php";

/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";
session_set_cookie_params(0,'/','.'. $config->domains->system , true, true);

/**
 * Read auto-loader
 */
include __DIR__ . "/../app/config/loader.php";

/**
 * Read services
 */
include __DIR__ . "/../app/config/services.php";

/**
 * Handle the request
 */
$application = new \Phalcon\Mvc\Application($di);
\Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
\Apprecie\Library\Security\Authentication::requestSourceSafe();
\Apprecie\Library\Translation\LanguageService::respondToLanguageChange();
\Apprecie\Library\Security\AccountLock::updateAccountLock();
echo $application->handle()->getContent();