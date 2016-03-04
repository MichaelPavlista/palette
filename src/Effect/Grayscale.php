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