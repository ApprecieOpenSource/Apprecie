<?php
return [
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'apprecie',
        'password'    => '@ppr3c13',
        'dbname'      => 'appreciedb',
        'charset'     => 'utf8',
        'persistent' => true
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
        'tests'          => APPLICATION_ROOT . 'tests/'
    ],
    'domains' => [
        'system' => 'apprecia.com'
    ],
    'environment' => [
        'fieldkey' => 'APPRECIE_FIELD_KEY'
    ]
];