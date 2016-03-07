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

$storage = require_once 'autoloader.php';

// PALETTE DEMO CODE
$pictureURL = $storage->loadPicture('picture.jpg@200&Enhance&Border;3;3;pink')->getUrl();

echo $pictureURL;
echo "<br /><br />";
echo "<img src='$pictureURL' alt='Palette' />";