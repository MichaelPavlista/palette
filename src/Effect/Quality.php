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
 * Class Quality
 * @package Palette\Effect
 */
class Quality extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'quality' => 100,
    );


    /**
     * Quality constructor.
     * @param int $quality
     */
    public function __construct($quality = 100) {

        $this->quality = abs($quality);
    }

    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $picture->quality($this->quality);
    }

}