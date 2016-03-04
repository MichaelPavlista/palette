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
use ImagickDraw;
use Imagick;

/**
 * Class Resize
 * @package InstagramEffect
 */
class Toaster extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $imagick = $picture->getResource(Picture::WORKER_IMAGICK);

        $this->colorize($imagick, '#330000', 0.6);

        $imagick->modulateImage(150, 80, 100);
        $imagick->gammaImage(1.2);
        $imagick->contrastImage(1);
        $imagick->contrastImage(1);

        $picture->setResource($imagick);
    }


    public function colorize(Imagick $image, $color, $alpha = 1) {

        $rectangle = new ImagickDraw();
        $rectangle->setFillColor($color);

        if(is_float($alpha)) {

            $rectangle->setFillAlpha($alpha);
        }

        $dimensions = $image->getImageGeometry();

        $rectangle->rectangle(0, 0, $dimensions['width'], $dimensions['height']);

        $image->drawImage($rectangle);
    }

}