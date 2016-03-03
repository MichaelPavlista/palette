<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Blur extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        for($i = 0; $i < 3; $i++) {

            imagefilter($gdResource, IMG_FILTER_GAUSSIAN_BLUR);
        }

        $picture->setResource($gdResource);
    }

}