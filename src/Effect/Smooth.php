<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Smooth extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'smooth' => NULL,
    );



    /**
     * Efekt úpravy hladkosti
     * @param $smooth
     */
    public function __construct($smooth) {

        $this->smooth = $smooth;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_SMOOTH, $this->smooth);

        $picture->setResource($gdResource);
    }

}