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
 * Class Resize
 * @package Effect
 */
class Border extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'width'  => NULL,
        'height' => NULL,
        'color'  => NULL,
    );



    /**
     * Efekt pro aplikování ohraničení obrázku
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
     * Aplikuje efekt na obrázek
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