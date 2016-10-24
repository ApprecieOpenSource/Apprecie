<?php
require_once($config->application->vendorDir . '/autoload.php');

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir,
        $config->application->partialsDir,
        "../app/widgets"
    )
)->register();

$loader->registerNamespaces(
    array(
        "Apprecie\Library"    => "../app/library/",
        "External\Vendor"     => "../vendor/"
    )
)->register();
