<?php

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