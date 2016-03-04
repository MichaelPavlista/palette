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
class Contrast extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'contrast' => NULL,
    );



    /**
     * Efekt změny kontrastu obrázku
     * @param $contrast
     */
    public function __construct($contrast) {

        $this->contrast = $contrast;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_CONTRAST, $this->contrast);

        $picture->setResource($gdResource);
    }

}