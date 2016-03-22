<?php

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