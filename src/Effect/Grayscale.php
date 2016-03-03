<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Grayscale extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        if($picture->isGd()) {

            imagefilter($picture->getResource(), IMG_FILTER_GRAYSCALE);
        }
        else {

            $picture->getResource()
                    ->modulateImage(100, 0, 100);
        }
    }

}