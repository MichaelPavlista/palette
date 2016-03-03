<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Sephia extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_GRAYSCALE);
        imagefilter($gdResource, IMG_FILTER_COLORIZE, 100, 50, 0);

        $picture->setResource($gdResource);
    }

}