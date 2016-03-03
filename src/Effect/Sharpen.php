<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Sharpen extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        $sharpen = array(

            array(0.0, -1.0, 0.0),
            array(-1.0, 5.0, -1.0),
            array(0.0, -1.0, 0.0),
        );

        imageconvolution($gdResource, $sharpen, array_sum(array_map('array_sum', $sharpen)), 0);

        $picture->setResource($gdResource);
    }

}