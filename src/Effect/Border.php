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
 * Class Border
 * @package Palette\Effect
 */
class Border extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'width'  => NULL,
        'height' => NULL,
        'color'  => NULL,
    );


    /**
     * Border effect constructor.
     * @param int $width
     * @param int $height
     * @param string $color
     */
    public function __construct($width = 1, $height = 1, $color = '#000') {

        $this->width  = $width;
        $this->height = $height;
        $this->color  = $color;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        if($picture->isGd()) {

        }
        else {

            $resource = $picture->getResource();

            $resize = new Resize($resource->getImageWidth() - $this->width * 2, $resource->getImageHeight() - $this->height * 2, Resize::MODE_STRETCH);
            $resize->apply($picture);

            $resource->borderImage($this->color, $this->width, $this->height);
        }
    }

}