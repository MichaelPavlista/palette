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
 * Class Sephia
 * @package Palette\Effect
 */
class Sephia extends PictureEffect {

    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $resource = $picture->getResource();

        if($picture->isGd()) {

            imagefilter($resource, IMG_FILTER_GRAYSCALE);
            imagefilter($resource, IMG_FILTER_COLORIZE, 100, 60, 0);

            $picture->setResource($resource);
        }
        else {

            $resource->sepiaToneImage(80);
        }
    }

}