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
use ImagickPixel;

/**
 * Class Rotate
 * @package Palette\Effect
 */
class Rotate extends PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array(

        'degrees'    => NULL,
        'background' => NULL,
    );


    /**
     * Rotate constructor.
     * @param $degrees
     * @param string $background
     */
    public function __construct($degrees, $background = NULL) {

        $this->degrees = $degrees;
        $this->background = $background;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $resource = $picture->getResource();

        if($picture->isGd()) {

            $transparentColor = imagecolorallocatealpha($resource, 0, 0, 0, 127);

            $resource = imagerotate($resource, -$this->degrees, $transparentColor);

            imagesavealpha($resource, true);

            // ADD IMAGE BACKGROUND COLOR AFTER ROTATION
            if($this->background && $this->background !== 'transparent') {

                $rgb   = $this->hex2rgb($this->background);
                $color = imagecolorallocate($resource, $rgb[0], $rgb[1], $rgb[2]);

                $width  = imagesx($resource);
                $height = imagesy($resource);

                $backgroundImage = imagecreatetruecolor($width, $height);

                imagefill($backgroundImage, 0, 0, $color);

                imagesavealpha($backgroundImage, TRUE);

                imagecopy($backgroundImage, $resource, 0, 0, 0, 0, $width, $height);

                imagedestroy($resource);

                $picture->setResource($backgroundImage);
                return;
            }

            $picture->setResource($resource);
        }
        else {

            $resource->rotateImage(new ImagickPixel('transparent'), $this->degrees);

            // ADD IMAGE BACKGROUND COLOR AFTER ROTATION (rotateImage is bugged)
            if($this->background && $this->background !== 'transparent') {

                $background = new Imagick();
                $background->setFormat('png');
                $background->newImage(

                    $resource->getImageWidth(),
                    $resource->getImageHeight(),
                    new ImagickPixel($this->background)
                );

                $background->compositeImage($resource, $resource->getImageCompose(), 0, 0);

                $picture->setResource($background);
            }
        }
    }


    /**
     * Calculates new picture dimension after applying this effect
     * @param int $width
     * @param int $height
     * @return array w,h
     */
    public function getNewDimensions($width, $height) {

        $radians = pi() * $this->degrees / 180;

        $NWL = $width * cos($radians);
        $NHL = $width * sin($radians);

        $NHU = $height * cos($radians);
        $NWR = $height * sin($radians);

        return parent::getNewDimensions(ceil(abs($NWL + $NWR)), ceil(abs($NHU + $NHL)));
    }

}