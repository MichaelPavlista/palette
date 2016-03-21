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
 * Class Blur
 * @package Palette\Effect
 */
class Blur extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'strength' => NULL,
    );


    /**
     * Blur constructor.
     * @param int $strength
     */
    public function __construct($strength = 2) {

        $this->strength = abs($strength);
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $resource = $picture->getResource();

        if($picture->isGd()) {

            // GIF IMAGES HAS PROBLEM WITH THIS FILTER
            if(strtolower(pathinfo($picture->getImage(), PATHINFO_EXTENSION)) === 'gif') {

                return;
            }

            for($i = 0; $i < $this->strength; $i++) {

                imagefilter($resource, IMG_FILTER_GAUSSIAN_BLUR);
            }

            $picture->setResource($resource);
        }
        else {

            $resource->blurImage(1 * $this->strength, 0.45 * $this->strength);
        }
    }

}