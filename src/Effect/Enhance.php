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
 * Class Enhance
 * @package Palette\Effect
 */
class Enhance extends PictureEffect
{
    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        $resource = $picture->getResource();

        if($picture->isGd())
        {
            imagefilter($resource, IMG_FILTER_CONTRAST, -15);
            imagefilter($resource, IMG_FILTER_BRIGHTNESS, 8);

            $picture->setResource($resource);
        }
        else
        {
            $resource->modulateImage(108, 170, 100);
        }
    }

}
