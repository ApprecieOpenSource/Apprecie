<?php
define('APPLICATION_ROOT', __DIR__ . '/../../');
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dev'));

if(! APPLICATION_ENV == 'dev' && ! APPLICATION_ENV == 'stage') {
    ini_set("display_errors", 0);
    ini_set("log_errors", 1);
}

$config = [
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'apprecie',
        'password'    => '@ppr3c13',
        'dbname'      => 'appreciedb',
        'charset'     => 'utf8',
        'persistent' => true
    ],
    'logging' => [
        'sqlLogEnabled' => false
    ],
    'application' => [
        'controllersDir' => APPLICATION_ROOT . 'app/controllers/',
        'modelsDir'      => APPLICATION_ROOT . 'app/models/',
        'viewsDir'       => APPLICATION_ROOT . 'app/views/',
        'pluginsDir'     => APPLICATION_ROOT . 'app/plugins/',
        'libraryDir'     => APPLICATION_ROOT . 'app/library/',
        'cacheDir'       => APPLICATION_ROOT . 'app/cache/',
        'baseUri'        => '/',
        'partialsDir'    => APPLICATION_ROOT . 'app/widgets/',
        'testLib'        => APPLICATION_ROOT . 'vendor/simpletest/simpletest',
        'vendorDir'      => APPLICATION_ROOT . 'vendor',
        'tests'          => APPLICATION_ROOT . 'tests/',
        'log'            => APPLICATION_ROOT . 'app/log/',
    ],
    'domains' => [
        'system' => 'apprecia.com'
    ],
    'environment' => [
        'fieldkey' => 'APPRECIE_FIELD_KEY',
        'defaultLanguageId' => 3,
        'timestampmax' => 2147472000
    ],
    'mail' => [
        'smtp' => 'smtp.sendgrid.net',
//        'user' => '<yourSENDGRIDuser>',
//        'pass' => "<yourSENDGRIDpassword>",
        'defaultFrom' => 'noreply@apprecie.com',
        'defaultSupport' => 'support@apprecie.com',
        'adminNotifications' => 'portaladmin@apprecie.com'
    ],
    'analytics' => [
//        'trackingId' => '<yourGAtrackingID'
    ],
    'stripe' => [
//        'secret_key' => '<yourSTRIPEsecretkey',
//        'publishable_key' => '<yourSTRIPEpublishablekey>',
//        'client_id' => '<yourSTRIPEcliendid>'
    ],
    'exceptions' => [
        'hide' =>'true',
        'controller' => 'error',
        'action' => 'exception'
    ],
    'authentication' => [
        'failedThreshold' => 10,
        'failedPeriod' => 180,
        'failedDelay' => 5
    ],
    'captcha' => [
        'failedThreshold' => 8,
        'failedPeriod' => 60
    ],
    'accountLock' => [
        'enabled' => true
    ],
    'security' => [
        'apiSharedKey' => 'jdi393mmkjk303owjf--ww83cFF12z'
    ],
    'pdfCrowd' => [
//        'user' => '<yourPDFCROWDuser>',
//        'password' => '<yourPDFCROWDpassword>'
    ],
    'postcodeAnywhere' => [
//        'apiKey' => '<yourPCAapikey>'
    ],
    'updatesAndNewsletters' => [
        'numberOfUsersToProcess' => 10,
        'numberOfItemsPerEmail' => 5
    ]
];

$config = new \Phalcon\Config($config);

// override config by environment if available
$envConfig = dirname(__FILE__). DIRECTORY_SEPARATOR . 'env' . DIRECTORY_SEPARATOR . APPLICATION_ENV .'.php';

if(file_exists($envConfig)) {
     $config->merge(new Phalcon\Config(require($envConfig)));
}

return $config;

