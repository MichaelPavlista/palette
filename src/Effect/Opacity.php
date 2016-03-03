<?php

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