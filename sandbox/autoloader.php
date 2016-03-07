<?php

/**
 * This file is part of the Palette (https://github.com/MichaelPavlista/palette)
 * Copyright (c) 2016 Michael Pavlista (http://www.pavlista.cz/)
 *
 * @author Michael Pavlista
 * @email  michael@pavlista.cz
 * @link   http://pavlista.cz/
 * @link   https://www.facebook.com/MichaelPavlista
 * @copyright 2016
 */

namespace Palette;

use Palette\Generator\Server;

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

/**
 * Palette sandbox autoloader
 */
spl_autoload_register(function($class) {

    $classFile = realpath(__DIR__ . '/../src/') . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $classFile = str_replace('/Palette', '', $classFile) . '.php';

    if(file_exists($classFile)) {

        require_once $classFile;
    }
});

// CREATE & RETURN SERVER STORAGE
return $storage = new Server(

    realpath(__DIR__ . '/thumbs/'),
    isset($_SERVER["HTTPS"]) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'thumbs/',
    __DIR__
);