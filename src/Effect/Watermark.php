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
 * Class Watermark
 * @package Palette\Effect
 */
class Watermark extends PictureEffect
{
    /** @var array effect settings */
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
     * Watermark constructor.
     * @param $watermark
     * @param string $position
     * @param float $opacity
     * @param null $size
     * @param int $space
     */
    public function __construct($watermark, $position = 'bottomRight', $opacity = 0.5, $size = NULL, $space = 10)
    {
        if(!in_array($position, array('repeat', 'center')) && $space)
        {
            $offset = explode(',', $space);

            $this->offsetX = $offset[0];
            $this->offsetY = empty($offset[1]) ? $offset[0] : $offset[1];
        }

        $this->watermark = realpath($watermark);
        $this->position = $position;
        $this->space = $space;
        $this->opacity = $opacity;
        $this->size = $size;
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
            $resource = $this->applyWatermarkGD($resource, $picture);

            $picture->setResource($resource);
        }
        else
        {
            $this->applyWatermarkImagick($resource);
        }
    }


    /**
     * Watermark effect processed by Imagick
     * @param Imagick $image
     */
    private function applyWatermarkImagick(Imagick $image)
    {
        $pictureWidth  = $image->getImageWidth();
        $pictureHeight = $image->getImageHeight();

        $watermarkPicture = new Picture($this->watermark, NULL, Picture::WORKER_IMAGICK);
        $watermarkPicture->quality(100);

        $opacity = new Opacity($this->opacity);
        $opacity->apply($watermarkPicture);

        if($this->size)
        {
            $resize = new Resize(
                $pictureWidth / 100 * $this->size,
                $pictureHeight / 100 * $this->size,
                Resize::MODE_FIT
            );

            $resize->apply($watermarkPicture);
        }

        $watermark = $watermarkPicture->getResource(Picture::WORKER_IMAGICK);

        $watermarkWidth  = $watermark->getImageWidth();
        $watermarkHeight = $watermark->getImageHeight();

        switch($this->position)
        {
            case 'repeat':

                for($w = 0; $w < $pictureWidth; $w += $watermarkWidth + $this->space)
                {
                    for($h = 0; $h < $pictureHeight; $h += $watermarkHeight + $this->space)
                    {
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


    /**
     * Watermark effect processed by gd
     * @param resource $resource gd image
     * @param Picture $picture
     * @return resource gd image
     */
    protected function applyWatermarkGD($resource, Picture $picture) {

        // SOURCE IMAGE DIMENSIONS
        $pictureWidth  = imagesx($resource);
        $pictureHeight = imagesy($resource);

        // WATERMARK DIMENSIONS
        $watermarkPicture = new Picture($this->watermark, NULL, Picture::WORKER_GD);
        $watermarkPicture->quality(100);

        $opacity = new Opacity($this->opacity);
        $opacity->apply($watermarkPicture);

        if($this->size)
        {
            $resize = new Resize(
                $pictureWidth / 100 * $this->size,
                $pictureHeight / 100 * $this->size,
                Resize::MODE_FIT
            );

            $resize->apply($watermarkPicture);
        }

        $watermark = $watermarkPicture->getResource($watermarkPicture::WORKER_GD);

        imagealphablending($watermark, TRUE);

        $watermarkWidth  = imagesx($watermark);
        $watermarkHeight = imagesx($watermark);

        // CALCULATE WATERMARK POSITION
        switch($this->position)
        {
            case 'repeat':

                for($w = 0; $w < $pictureWidth; $w += $watermarkWidth + $this->space)
                {
                    for($h = 0; $h < $pictureHeight; $h += $watermarkHeight + $this->space)
                    {
                        imagecopy($resource, $watermark, $w, $h, 0, 0, $watermarkWidth, $watermarkHeight);
                    }
                }

                return $resource;

            case 'center':

                $positionX = ($pictureWidth - $watermarkWidth) / 2 - $this->offsetX;
                $positionY = ($pictureHeight - $watermarkHeight) / 2 - $this->offsetY;
                break;

            case 'topRight':

                $positionX = $pictureWidth - $watermarkWidth - $this->offsetX;
                $positionY = $this->offsetY;
                break;

            case 'bottomRight':

                $positionX = $pictureWidth  - $watermarkWidth  - $this->offsetX;
                $positionY = $pictureHeight - $watermarkHeight - $this->offsetY;
                break;

            case 'bottomLeft':

                $positionX = $this->offsetX;
                $positionY = $pictureHeight - $watermarkHeight - $this->offsetY;
                break;

            default:

                $positionX = $this->offsetX;
                $positionY = $this->offsetY;
                break;
        }

        imagecopy($resource, $watermark, $positionX, $positionY, 0, 0, $watermarkWidth, $watermarkHeight);

        return $resource;
    }

}
