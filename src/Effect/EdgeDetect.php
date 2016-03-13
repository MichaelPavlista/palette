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
 * Class EdgeDetect
 * @package Palette\Effect
 */
class EdgeDetect extends PictureEffect {

    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        // GD VERSION IS BETTER AND IS PREFERRED
        if($picture->isGd() || $picture->gdAvailable()) {

            $resource = $picture->getResource($picture::WORKER_GD);

            imagefilter($resource, IMG_FILTER_EDGEDETECT);

            $picture->setResource($resource);
        }
        else {

            $resource = $picture->getResource($picture::WORKER_IMAGICK);
            $resource->convolveImage([

                -1,-1,-1,-1,8,-1,-1,-1,-1
            ]);
            $resource->thresholdImage(1);
        }
    }

}