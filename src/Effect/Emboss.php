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
 * Class Emboss
 * @package Palette\Effect
 */
class Emboss extends PictureEffect
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

            imagefilter($resource, IMG_FILTER_EMBOSS);

            $picture->setResource($resource);
        }
        else
        {
            // EMBOSS IS NOT SUPPORTED IN IMAGICK
        }
    }

}
