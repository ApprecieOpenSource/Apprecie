<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileLogger;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
use \Phalcon\Mvc\Dispatcher;
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);


/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            $compiler = $volt->getCompiler();
            $compiler->addFunction('widget', function($resolvedArgs, $exprArgs) use ($compiler) {
                    if(isset($exprArgs[0])) $first = $compiler->expression($exprArgs[0]['expr']);
                    else return 'widget call without widget name';

                    if(isset($exprArgs[1])) $second = $compiler->expression($exprArgs[1]['expr']);
                    else $second = "'index'";

                    if(isset($exprArgs[2])) $third = $compiler->expression($exprArgs[2]['expr']);
                    else $third = null;

                    if($third == null) {
                        return 'Apprecie\Library\Widgets\WidgetManager::get(' . $first . ',' . $second . ')->getContent()';
                    }

                    return 'Apprecie\Library\Widgets\WidgetManager::get(' . $first . ',' . $second . ',' . $third . ')->getContent()';
                });

            $compiler->addFunction('csrf', function()  {
                    return 'Apprecie\Library\Security\CSRFProtection::csrf()';
            });

            $compiler->addFunction('fd', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return 'Apprecie\Library\Localisation\DateTimeHelper::getDateFromMySQLDateTimeString(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
            });

            $compiler->addFunction('ft', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return 'Apprecie\Library\Localisation\DateTimeHelper::getTimeFromMySQLDateTimeString(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
            });

            $compiler->addFunction('fdt', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return 'Apprecie\Library\Localisation\DateTimeHelper::getDateTimeFromMySQLDateTimeString(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
            });

            $compiler->addFunction('hd', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_hd(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
            });

            $compiler->addFunction('hdt', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_hdt(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
            });

            $compiler->addFunction('eh', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_eh(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
                });

            $compiler->addFunction('eha', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_eha(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
                });

            $compiler->addFunction('ej', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_ehj(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
                });

            $compiler->addFunction('ec', function($resolvedArgs, $exprArgs) use ($compiler) {
                    return '_ec(' .  $compiler->expression($exprArgs[0]['expr']) . ')';
                });

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

$di->setShared('config', $config);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
    if($config->logging->sqlLogEnabled) {
        $eventsManager = new Phalcon\Events\Manager();
        $logger = new FileLogger($config->application->log . 'sql.log');
    }

    try{
        $dbConnection = new DbAdapter(array(
            'host' => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname' => $config->database->dbname,
            'charset' => $config->database->charset,
            'persistent' => $config->database->persistent,
        ));
    } catch (Exception $ex) {
        _d('The data layer has gone way,  possible network / infrastructure issue.  Please try again shortly.');
    }


    if($config->logging->sqlLogEnabled) {
        $eventsManager->attach(
            'db',
            function ($event, $dbConnection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sqlVariables = $dbConnection->getSQLVariables();
                    if(count($sqlVariables) == 0) {
                        $logger->log($dbConnection->getSQLStatement(), Logger::INFO);
                    } else {
                        $logger->log($dbConnection->getSQLStatement() . '::' . join(',', $sqlVariables), Logger::INFO);
                    }
                }
            }
        );

        $dbConnection->setEventsManager($eventsManager);
    }

    return $dbConnection;
});

$di->set(
    'modelsMetadata',
    function () use ($config) {
        return new \Apprecie\Library\Metadata\Wincache([ "lifetime" => 86400]) ;
    },
    true
);

$di->setShared('request', function () {
        return new \Apprecie\Library\Request\RequestEx();
});

$di->setShared('escape', function () {
        return new \Phalcon\Escaper();
});

$di->setShared('response', function () {
        return new \Apprecie\Library\Response\ResponseEx();
});

$di->setShared('translation', function() use ($di) {
        return new \Apprecie\Library\Translation\TranslationDBAdapter([
            'db' => $di->get('db'),
            'table' => 'translations',
            'language' => (new \Phalcon\Http\Request())->getBestLanguage()
        ]);
    });

$di->setShared('encRegistry', function() {
        return new \Apprecie\Library\Security\EncryptionRegistry();
});

$di->setShared('userRegistry', function() {
    return new \Apprecie\Library\Users\UserEntityRegistry('ent_user');
});

$di->setShared('userProfileRegistry', function() {
    return new \Apprecie\Library\Users\UserEntityRegistry('ent_user_profile');
});

$di->setShared('userPortalRegistry', function() {
    return new \Apprecie\Library\Users\UserEntityRegistry('ent_portal_user');
});

$di->setShared('userLoginRegistry', function() {
    return new \Apprecie\Library\Users\UserEntityRegistry('ent_user_login');
});

$di->setShared('activitylog', function () {
    return new \Apprecie\Library\Tracing\ActivityTrace();
});

$di->setShared('outputcache', function () {
    $frontCache = new Phalcon\Cache\Frontend\Output();
    $cache = new \Apprecie\Library\Cache\Wincache($frontCache);
    return $cache;
});

$di->setShared('contentresolver', function () {
        return new \Apprecie\Library\Translation\ContentResolver();
});

$di->setShared('cache', function () {
    $frontCache = new Phalcon\Cache\Frontend\None();
    $cache = new \Apprecie\Library\Cache\Wincache($frontCache);
    return $cache;
});

$di->setShared('fieldkey', function () use ($config) {
    return getenv($config->environment->fieldkey);
});

/**
 * obtain portal settings - based on subdomain
 */
$di->set('portal', function () {
    return Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
});

$di->set('portalid', function () {
   return Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () use ($di, $config) {
    $dbConnection = $di->get('db');
    $sessionHandler = new Apprecie\Library\Session\MySQLHandler(['db'=>$dbConnection, 'table'=>'sessiondata']);

    $sessionHandler->start();
    return $sessionHandler;
});

$di->setShared('auth', function () {
   return new \Apprecie\Library\Security\Authentication();
});

$di->set('domains', function () use ($config) {
    return $config->domains;
});

$di->set('router', function () {
    $router = new \Phalcon\Mvc\Router();
    $router->setDefaultController('Login');
    $router->setDefaultAction('index');
    return $router;
});

$di->set(
    'dispatcher',
    function() use ($di, $config) {
        $eventsManager = $di->getShared('eventsManager');

        $eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) {
            header_remove("X-Powered-By");
            $di = $dispatcher->getDI();
            $di->get('response')->setHeader('X-Frame-Options', 'SAMEORIGIN');

            if ($di->get('session')->has('TERMS_UNACCEPTED') && $di->get('session')->get('TERMS_UNACCEPTED') !== null && $dispatcher->getControllerName() !== 'legal' && $dispatcher->getControllerName() !== 'login') {
                $dispatcher->forward(
                    array(
                        'controller' => 'legal',
                        'action' => 'accept'
                    )
                );
            }
            return true;
        });

        $eventsManager->attach(
            'dispatch:beforeException',
            function($event, $dispatcher, $exception) use($config) {
                $log = new ActivityLog();
                switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND: {

                        $log->logActivity('404', $exception->getMessage());
                        $dispatcher->forward(
                            array(
                                'controller' => 'Error',
                                'action' => 'fourofour'
                            )
                        );
                        return false;
                        break;
                    }
                    default: {
                        $log->logActivity('500', $exception->getMessage());
                        if($config->exceptions->hide == 'true') {
                            $dispatcher->forward(
                                array(
                                    'controller' => $config->exceptions->controller,
                                    'action' => $config->exceptions->action
                                )
                            );
                            return false;
                        }
                        break;
                    }
                }
            }
        );
        $dispatcher = new Dispatcher();
        $dispatcher->setEventsManager($eventsManager);
        return $dispatcher;
    },
    true
);
