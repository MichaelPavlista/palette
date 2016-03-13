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

/**
 * Class Brightness
 * @package Palette\Effect
 */
class Brightness extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'brightness' => NULL,
    );


    /**
     * Brightness constructor.
     * @param int $brightness
     */
    public function __construct($brightness) {

        $this->brightness = $brightness;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $resource = $picture->getResource();

        if($picture->isGd()) {

            imagefilter($resource, IMG_FILTER_BRIGHTNESS, ceil($this->brightness * 2.55));

            $picture->setResource($resource);
        }
        else {

            $resource->modulateImage(100 + $this->brightness, 100, 100);
        }
    }

}