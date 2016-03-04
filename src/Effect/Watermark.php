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
use Imagick;

/**
 * Class Resize
 * @package Effect
 */
class Watermark extends PictureEffect {

    /**
     * @var array nastavení tohoto filtru
     */
    protected $settings = array(

        'watermark' => NULL,
        'position'  => NULL,
        'opacity'   => NULL,
        'size'      => NULL,
        'space'     => NULL,
        'offsetX'   => 0,
        'offsetY'   => 0,
    );



    /**
     * Efekt obrázkového vodoznaku
     * @param $watermark
     * @param string $position
     * @param float $opacity
     * @param null $size
     * @param int $space
     */
    public function __construct($watermark, $position = 'bottomRight', $opacity = 0.5, $size = NULL, $space = 10) {

        if(!in_array($position, array('repeat', 'center')) && $space) {

            $offset = explode(',', $space);

            $this->offsetX = $offset[0];
            $this->offsetY = empty($offset[1]) ? $offset[0] : $offset[1];
        }

        $this->watermark = realpath($watermark);
        $this->position  = $position;
        $this->space     = $space;
        $this->opacity   = $opacity;
        $this->size      = $size;
    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        if($picture->isGd()) {

        }
        else {

            $this->watermarkImagick($picture->getResource());
        }
    }


    /**
     * Přidá vodoznak na obrázek pomocí knihovny Imagick
     * @param Imagick $image
     */
    private function watermarkImagick(Imagick $image) {

        $watermark = new Imagick($this->watermark);
        $watermark->evaluateImage(Imagick::EVALUATE_MULTIPLY, $this->opacity, Imagick::CHANNEL_ALPHA);

        if($this->size) {

            $watermark->scaleImage(
                $image->getImageWidth() / 100 * $this->size,
                $image->getImageHeight() / 100 * $this->size,
                TRUE
            );
        }

        switch($this->position) {

            case 'repeat':

                for($w = 0; $w < $image->getImageWidth(); $w += $watermark->getImageWidth() + $this->space) {

                    for($h = 0; $h < $image->getImageHeight(); $h += $watermark->getImageHeight() + $this->space) {

                        $image->compositeImage($watermark, $watermark->getImageCompose(), $w, $h);
                    }
                }
            return;

            case 'center':

                $positionX = ($image->getImageWidth() - $watermark->getImageWidth()) / 2 - $this->offsetX;
                $positionY = ($image->getImageHeight() - $watermark->getImageHeight()) / 2 - $this->offsetY;
                break;

            case 'topRight':

                $positionX = $image->getImageWidth() - $watermark->getImageWidth() - $this->offsetX;
                $positionY = $this->offsetY;
                break;

            case 'bottomRight':

                $positionX = $image->getImageWidth()  - $watermark->getImageWidth()  - $this->offsetX;
                $positionY = $image->getImageHeight() - $watermark->getImageHeight() - $this->offsetY;
                break;

            case 'bottomLeft':

                $positionX = $this->offsetX;
                $positionY = $image->getImageHeight() - $watermark->getImageHeight() - $this->offsetY;
                break;

            default:

                $positionX = $this->offsetX;
                $positionY = $this->offsetY;
                break;
        }

        $image->compositeImage($watermark, $watermark->getImageCompose(), $positionX, $positionY);
    }

}