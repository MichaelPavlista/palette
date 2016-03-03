<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Mosaic extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_MEAN_REMOVAL);
        imagefilter($gdResource, IMG_FILTER_CONTRAST, - 50);

        $picture->setResource($gdResource);
    }

}