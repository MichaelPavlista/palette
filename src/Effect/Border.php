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
 * Class Border
 * @package Palette\Effect
 */
class Border extends PictureEffect
{
    /** @var array effect settings */
    protected $settings = array(

        'width'  => NULL,
        'height' => NULL,
        'color'  => NULL,
    );


    /**
     * Border effect constructor.
     * @param int $width
     * @param int $height
     * @param string $color
     */
    public function __construct($width = 1, $height = 1, $color = '#000')
    {
        $this->width  = (int) $width;
        $this->height = (int) $height;
        $this->color  = $color;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        $resource = $picture->getResource();

        if($picture->isGd())
        {
            $resize = new Resize(

                imagesx($resource) - $this->width  * 2,
                imagesy($resource) - $this->height * 2,
                Resize::MODE_STRETCH
            );

            $resize->apply($picture);

            $resource = $picture->getResource();

            imagesavealpha($resource, TRUE);

            $width  = imagesx($resource);
            $height = imagesy($resource);

            $borderedWidth  = $width  + $this->width * 2;
            $borderedHeight = $height + $this->height * 2;

            $borderColor = $this->hex2rgb($this->color);
            $borderColor = imagecolorallocate($resource, $borderColor[0], $borderColor[1], $borderColor[2]);

            $borderedImage = imagecreatetruecolor($borderedWidth, $borderedHeight);
            $transparent = imagecolorallocatealpha($borderedImage, 0, 0, 0, 127);

            imagesavealpha($borderedImage, TRUE);
            imagefill($borderedImage, 0, 0, $transparent);

            // BORDER: TOP x BOTTOM x LEFT x RIGHT
            imagefilledrectangle($borderedImage, 0, 0, $borderedWidth - 1, $this->height - 1, $borderColor);
            imagefilledrectangle($borderedImage, 0, $borderedHeight - 1, $borderedWidth - 1, $borderedHeight - $this->height, $borderColor);
            imagefilledrectangle($borderedImage, 0, 0, $this->width - 1, $borderedHeight - 1, $borderColor);
            imagefilledrectangle($borderedImage, $borderedWidth - 1, 0, $borderedWidth - $this->width, $borderedHeight - 1, $borderColor);

            imagecopyresampled($borderedImage, $resource, $this->width, $this->height, 0, 0, $width, $height, $width, $height);

            $picture->setResource($borderedImage);
        }
        else
        {
            $width  = $resource->getImageWidth();
            $height = $resource->getImageHeight();

            $resize = new Resize(

                $resource->getImageWidth()  - $this->width  * 2,
                $resource->getImageHeight() - $this->height * 2,
                Resize::MODE_STRETCH
            );

            $resize->apply($picture);

            $borderedImage = new \Imagick();
            $borderedImage->setFormat('png');
            $borderedImage->newImage($width, $height, new \ImagickPixel('transparent'));

            // BORDER: TOP x BOTTOM x LEFT x RIGHT
            $border = new \ImagickDraw();
            $border->setFillColor(new \ImagickPixel($this->color));
            $border->rectangle(0, 0, $width - 1, $this->height - 1);
            $border->rectangle(0, $height - $this->height, $width - 1, $height - 1);
            $border->rectangle(0, 0, $this->width - 1, $height - 1);
            $border->rectangle($width - $this->width, 0, $width - 1, $height - 1);

            $borderedImage->compositeImage($resource, $resource->getImageCompose(), $this->height, $this->width);
            $borderedImage->drawImage($border);

            $picture->setResource($borderedImage);
        }
    }

}
