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
 * Class Opacity
 * @package Palette\Effect
 */
class Opacity extends PictureEffect
{
    /** @var array effect settings */
    protected $settings = array(

        'opacity' => NULL,
    );


    /**
     * Opacity constructor.
     * @param float $opacity
     */
    public function __construct($opacity)
    {
        $this->opacity = $opacity;
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
            imagesavealpha($resource, TRUE);
            imagealphablending($resource, FALSE);

            $w = imagesx($resource);
            $h = imagesy($resource);

            // FIND SMALLEST ALPHA VALUE
            $minimalAlpha = 127;

            for($x = 0; $x < $w; $x++) {

                for($y = 0; $y < $h; $y++)
                {
                    $alpha = (imagecolorat($resource, $x, $y) >> 24 ) & 0xFF;

                    if($alpha < $minimalAlpha)
                    {
                        $minimalAlpha = $alpha;
                    }
                }
            }

            // MODIFY IMAGE PIXELS
            for($x = 0; $x < $w; $x++)
            {
                for($y = 0; $y < $h; $y++)
                {
                    $colorXY = imagecolorat($resource, $x, $y);
                    $alpha = ($colorXY >> 24) & 0xFF;

                    if($minimalAlpha !== 127)
                    {
                        $alpha = 127 + 127 * $this->opacity * ($alpha - 127) / (127 - $minimalAlpha);
                    }
                    else
                    {
                        $alpha += 127 * $this->opacity;
                    }

                    $alphaColorXY = imagecolorallocatealpha(

                        $resource,
                        ($colorXY >> 16) & 0xFF,
                        ($colorXY >> 8) & 0xFF,
                        $colorXY & 0xFF,
                        $alpha
                    );

                    // MODIFY SINGLE PIXEL VALUE
                    imagesetpixel($resource, $x, $y, $alphaColorXY);
                }
            }

            $picture->setResource($resource);
        }
        else
        {
            if(!$resource->getImageAlphaChannel())
            {
                $resource->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
            }

            $resource->evaluateImage(Imagick::EVALUATE_MULTIPLY, $this->opacity, Imagick::CHANNEL_ALPHA);
        }
    }

}
