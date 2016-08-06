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
 * Class Colorize
 * @package Palette\Effect
 */
class Colorize extends PictureEffect
{
    /** @var array effect settings */
    protected $settings = array(

        'color' => NULL,
    );


    /**
     * Colorize constructor.
     * @param string $color
     */
    public function __construct($color)
    {
        $this->color = preg_replace('/\s+/', '', $color);
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        // CALCULATE COLORIZE COLOR
        if(strpos($this->color, '#') !== FALSE)
        {
            $color = $this->hex2rgb($this->color);
        }
        else
        {
            $color = explode(',', $this->color);
        }

        // GD VERSION IS BETTER AND IS PREFERRED
        if($picture->isImagick() && !$picture->gdAvailable())
        {
            $resource = $picture->getResource($picture::WORKER_IMAGICK);

            $quantumRange = $resource->getQuantumRange();

            $r = $this->normalizeChannel($color[0]);
            $g = $this->normalizeChannel($color[1]);
            $b = $this->normalizeChannel($color[2]);

            $resource->levelImage(0, $r, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_RED);
            $resource->levelImage(0, $g, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_GREEN);
            $resource->levelImage(0, $b, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_BLUE);
        }
        else
        {
            $resource = $picture->getResource($picture::WORKER_GD);

            $r = ceil($color[0]);
            $g = ceil($color[1]);
            $b = ceil($color[2]);

            imagefilter($resource, IMG_FILTER_COLORIZE, $r, $g, $b);

            $picture->setResource($resource);
        }
    }


    /**
     * Normalize color chanel value for imagick
     * @param $value
     * @return float
     */
    protected function normalizeChannel($value)
    {
        $value = ceil($value / 2.55);

        if($value > 0)
        {
            return $value / 5;
        }
        else
        {
            return ($value + 100) / 100;
        }
    }

}
