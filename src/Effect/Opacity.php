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
class Opacity extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'opacity' => NULL,
    );



    /**
     * Efekt změny průhlednosti obrázku
     * @param float $opacity průhlednost
     */
    public function __construct($opacity) {

        $this->opacity = $opacity;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        if($picture->isGd()) {

        }
        else {

            $picture->getResource()
                    ->setImageOpacity($this->opacity);
        }
    }

}