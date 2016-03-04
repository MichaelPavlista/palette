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
class Colorize extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'color' => NULL,
    );



    /**
     * Efekt zbarvení obrázku do určité barvy
     * @param $color
     */
    public function __construct($color) {

        $this->color = preg_replace('/\s+/', '', $color);
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        if(strpos($this->color, '#') !== FALSE) {

            $color = $this->hex2rgb($this->color);
        }
        else {

            $color = explode(',', $this->color);
        }

        imagefilter($gdResource, IMG_FILTER_COLORIZE, $color[0], $color[1], $color[2]);

        $picture->setResource($gdResource);
    }

}