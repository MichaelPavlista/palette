<?php

namespace Palette\Effect;

use Palette\Picture;

/**
 * Class Resize
 * @package Effect
 */
class Brightness extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'brightness' => NULL,
    );



    /**
     * Efekt změny jasu obrázku
     * @param int $brightness
     */
    public function __construct($brightness) {

        $this->brightness = $brightness;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $gdResource = $picture->getResource(Picture::WORKER_GD);

        imagefilter($gdResource, IMG_FILTER_BRIGHTNESS, $this->brightness);

        $picture->setResource($gdResource);
    }

}