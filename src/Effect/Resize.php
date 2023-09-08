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

use Imagick;
use Palette\Picture;

/**
 * Class Resize
 * @package Palette\Effect
 */
class Resize extends PictureEffect
{
    /** @const image resize mode */
    const MODE_FIT = 'fit';

    /** @const image resize mode */
    const MODE_FILL = 'fill';

    /** @const image resize mode */
    const MODE_STRETCH = 'stretch';

    /** @const image resize mode */
    const MODE_CROP = 'crop';

    /** @const image resize mode */
    const MODE_EXACT = 'exact';

    /** @var array effect settings */
    protected $settings = array(

        'width'  => NULL,
        'height' => NULL,
        'resizeMode' => NULL,
        'resizeSmaller' => NULL,
        'color' => NULL,
    );


    /**
     * Resize constructor.
     * @param $width
     * @param int|null $height
     * @param int|null $resizeMode
     * @param int $resizeSmaller
     * @param string $color
     */
    public function __construct($width, $height = NULL, $resizeMode = NULL, $resizeSmaller = 0, $color = NULL)
    {
        if(!$height)
        {
            $height = $width;
        }

        $this->width  = (int) $width;
        $this->height = (int) $height;
        $this->resizeMode = $resizeMode;
        $this->resizeSmaller = $resizeSmaller ? 1 : 0;
        $this->color = $color;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        if($picture->isGd())
        {
            $resource = $this->resizeGd($picture->getResource());

            $picture->setResource($resource);
        }
        else
        {
            $this->resizeImagick($picture->getResource(), $picture);
        }
    }


    /**
     * Calculates new picture dimension after applying this effect
     * @param int $width
     * @param int $height
     * @return array w,h
     */
    public function getNewDimensions($width, $height)
    {
        if(!$this->resizeSmaller && $this->height >= $height && $this->width >= $width)
        {
            return parent::getNewDimensions($width, $height);
        }

        if(in_array($this->resizeMode, array($this::MODE_CROP, $this::MODE_STRETCH, $this::MODE_EXACT)))
        {
            return parent::getNewDimensions($this->width, $this->height);
        }
        elseif($this->resizeMode === $this::MODE_FILL)
        {
            $ratioH = $this->height / $height;
            $ratioW = $this->width / $width;

            $widthFill  = max($width * $ratioH, $width * $ratioW);
            $heightFill = max($height * $ratioH, $height * $ratioW);
            $ratio = max($widthFill / $width, $heightFill / $height);

            return parent::getNewDimensions(round($width * $ratio), round($height * $ratio));
        }
        else
        {
            $pictureWidth  = $this->width;
            $pictureHeight = $this->height;

            if($width > $pictureWidth || $height > $pictureHeight)
            {
                if($width > $height)
                {
                    $pictureHeight = floor(($height / $width) * $pictureWidth);
                }
                elseif($width < $height)
                {
                    $pictureWidth = floor(($width / $height) * $pictureHeight);
                }
            }

            return parent::getNewDimensions($pictureWidth, $pictureHeight);
        }
    }


    /**
     * Resizing an image using Imagick
     * @param Imagick $imagick
     */
    private function resizeImagick(Imagick $imagick, Picture $picture)
    {
        if(!$this->resizeSmaller && $this->height > $imagick->getImageHeight() && $this->width > $imagick->getImageWidth())
        {
            return;
        }

        if($this->resizeMode === $this::MODE_CROP)
        {
            $imagick->cropThumbnailImage($this->width, $this->height);
        }
        elseif($this->resizeMode === $this::MODE_FILL)
        {
            $ratioH = $this->height / $imagick->getImageHeight();
            $ratioW = $this->width / $imagick->getImageWidth();

            $width  = max($imagick->getImageWidth() * $ratioH, $imagick->getImageWidth() * $ratioW);
            $height = max($imagick->getImageHeight() * $ratioH, $imagick->getImageHeight() * $ratioW);
            $ratio  = max($width / $imagick->getImageWidth(), $height / $imagick->getImageHeight());

            $imagick->scaleImage(round($imagick->getImageWidth() * $ratio), round($imagick->getImageHeight() * $ratio), TRUE);
        }
        elseif($this->resizeMode === $this::MODE_STRETCH)
        {
            $imagick->scaleImage($this->width, $this->height, FALSE);
        }
        elseif($this->resizeMode === $this::MODE_EXACT)
        {
            $imagick->scaleImage($this->width, $this->height, TRUE);

            $rectangle = $picture->createImagick();
            $rectangle->setFormat('png');
            $rectangle->newImage($this->width, $this->height, new \ImagickPixel($this->color ?: 'transparent'));
            $rectangle->compositeImage($imagick, $imagick->getImageCompose(),

                ($rectangle->getImageWidth() - $imagick->getImageWidth()) / 2,
                ($rectangle->getImageHeight() - $imagick->getImageHeight()) / 2
            );

            $picture->setResource($rectangle);
        }
        else
        {
            $imagick->scaleImage($this->width, $this->height, TRUE);
        }
    }


    /**
     * Resizing an image using GD
     * @param $resource
     * @return resource
     */
    private function resizeGd($resource)
    {
        $origWidth  = imagesx($resource);
        $origHeight = imagesy($resource);

        if(!$this->resizeSmaller && $this->height > $origHeight && $this->width > $origWidth)
        {
            return $resource;
        }

        $resizeX = 0;
        $resizeY = 0;

        $realWidth  = $this->width;
        $realHeight = $this->height;

        if($this->resizeMode === $this::MODE_FILL)
        {
            $ratioH = $this->height / $origHeight;
            $ratioW = $this->width / $origWidth;

            $width  = max($origWidth * $ratioH, $origWidth * $ratioW);
            $height = max($origHeight * $ratioH, $origHeight * $ratioW);
            $ratio  = max($width / $origWidth, $height / $origHeight);

            $realWidth  = round($origWidth * $ratio);
            $realHeight = round($origHeight * $ratio);
        }
        elseif($this->resizeMode === $this::MODE_EXACT)
        {
            $ratioW = $this->width / $origWidth;
            $ratioH = $this->height / $origHeight;

            $exactRatio = min($ratioW, $ratioH);

            $resizeWidth  = round($origWidth * $exactRatio);
            $resizeHeight = round($origHeight * $exactRatio);

            $resizeX = floor(($this->width - $resizeWidth) / 2);
            $resizeY = floor(($this->height - $resizeHeight) / 2);

            $pictureResized = imagecreatetruecolor($this->width, $this->height);

            imagealphablending($pictureResized, TRUE);

            if(!$this->color || $this->color === 'transparent') {

                $color = imagecolorallocatealpha($pictureResized, 0, 0, 0, 127);
            }
            else {

                $rgb = $this->hex2rgb($this->color);

                $color = imagecolorallocate($pictureResized, $rgb[0], $rgb[1], $rgb[2]);
            }

            imagefill($pictureResized, 0, 0, $color);

            imagesavealpha($pictureResized, true);

            imagecopyresampled($pictureResized, $resource, $resizeX, $resizeY, 0, 0, $resizeWidth, $resizeHeight, $origWidth, $origHeight);

            return $pictureResized;
        }
        elseif($this->resizeMode === $this::MODE_CROP)
        {
            if(($origWidth / $origHeight) > ($this->width / $this->height)) {

                $widthTmp = $origHeight * $this->width / $this->height;

                $resizeX = ($origWidth - $widthTmp) / 2;
                $origWidth = $widthTmp;
            }
            elseif(($origWidth / $origHeight) < ($this->width / $this->height)) {

                $heightTmp = $origWidth * $this->height / $this->width;

                $resizeY = ($origHeight - $heightTmp) / 2;
                $origHeight = $heightTmp;
            }
        }
        // DEFAULT RESIZE METHOD FIT
        elseif($this->resizeMode !== $this::MODE_STRETCH)
        {
            $widthRatio  = $origWidth / $this->width;
            $heightRatio = $origHeight / $this->height;

            $resizeRatio = max($widthRatio, $heightRatio);

            $realWidth  = floor($origWidth / $resizeRatio);
            $realHeight = floor($origHeight / $resizeRatio);
        }

        $pictureResized   = imagecreatetruecolor($realWidth, $realHeight);
        $transparentColor = imagecolorallocatealpha($pictureResized, 0, 0, 0, 127);

        imagefill($pictureResized, 0, 0, $transparentColor);

        imagesavealpha($pictureResized, true);

        imagecopyresampled($pictureResized, $resource, 0, 0, $resizeX, $resizeY, $realWidth, $realHeight, $origWidth, $origHeight);

        return $pictureResized;
    }

}
