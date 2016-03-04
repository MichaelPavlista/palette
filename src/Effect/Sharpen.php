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