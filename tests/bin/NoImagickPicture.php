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

/**
 * Class TestPicture
 */
class NoImagickPicture extends Palette\Picture {

    /**
     * Is PHP extension Imagick available?
     * @return bool
     */
    public static function imagickAvailable() {

        return FALSE;
    }

}