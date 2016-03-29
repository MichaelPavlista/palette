<?php

// RUN TESTS BY THIS COMMAND:
// vendor/bin/tester vendor/pavlista/palette/tests/ -p php -c vendor/pavlista/palette/tests/php.ini

set_time_limit(600);
ini_set('memory_limit','1024M');

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .
$devPath  = realpath('../../vendor/autoload.php');
$livePath = realpath(__DIR__ . '/../../../autoload.php');

$autoloaderPath = $livePath ?: $devPath;

if(!@include $autoloaderPath) {

    echo 'Install Nette Tester using `composer install`';
    exit(1);
}

require_once 'bin/NoImagickPicture.php';

// configure environment
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

// create temporary directory
define('TEMP_DIR', __DIR__ . '/tmp/' . lcg_value());
@mkdir(TEMP_DIR, 0777, TRUE);

register_shutdown_function(function () {

    Tester\Helpers::purge(TEMP_DIR);
    rmdir(TEMP_DIR);
});