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
 * Class Contrast
 * @package Palette\Effect
 */
class Contrast extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'contrast' => NULL,
    );


    /**
     * Contrast constructor.
     * @param int $contrast
     */
    public function __construct($contrast) {

        $this->contrast = $contrast;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_CONTRAST, $this->contrast);

        $picture->setResource($gdResource);
    }

}