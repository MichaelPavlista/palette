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

namespace Palette\Effect;

use Palette\Picture;
use ImagickPixel;

/**
 * Class Resize
 * @package Effect
 */
class Rotate extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'degrees'    => NULL,
        'background' => NULL,
    );



    /**
     * Efekt otočení obrázku
     * @param $degrees
     * @param string $background
     */
    public function __construct($degrees, $background = '#FFF') {

        $this->degrees    = $degrees;
        $this->background = $background;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        if($picture->isGd()) {

        }
        else {

            $picture->getResource()
                    ->rotateImage(new ImagickPixel($this->background), $this->degrees);
        }
    }


    /**
     * @param $width
     * @param $height
     * @return array
     */
    public function getNewDimensions($width, $height) {

        $radians = pi() * $this->degrees / 180;

        $NWL = $width * cos($radians);
        $NHL = $width * sin($radians);

        $NHU = $height * cos($radians);
        $NWR = $height * sin($radians);

        return parent::getNewDimensions(ceil(abs($NWL + $NWR)), ceil(abs($NHU + $NHL)));
    }

}