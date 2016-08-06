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
 * Class Negative
 * @package Palette\Effect
 */
class Negative extends PictureEffect
{
    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        if($picture->isGd() || $picture->gdAvailable())
        {
            $resource = $picture->getResource($picture::WORKER_GD);

            imagefilter($resource, IMG_FILTER_NEGATE);

            $picture->setResource($resource);
        }
        else
        {
            // NEGATIVE IS NOT SUPPORTED IN IMAGICK
        }
    }

}
