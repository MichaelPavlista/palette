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
 * Class Colorspace
 * @package Palette\Effect
 */
class Colorspace extends PictureEffect
{
    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        if($picture->imagickAvailable())
        {
            $image = $picture->getResource(Picture::WORKER_IMAGICK);

            if($image->getImageColorspace() == Imagick::COLORSPACE_CMYK)
            {
                $profiles = $image->getImageProfiles('*', FALSE);

                $path = realpath(__DIR__ . '/../Profiles/') . DIRECTORY_SEPARATOR;

                if(array_search('icc', $profiles) === FALSE)
                {
                    $image->profileImage('icc', file_get_contents($path . 'USWebUncoated.icc'));
                }

                $image->profileImage('icc', file_get_contents($path . 'sRGB _Color_Space_Profile.icm'));
            }

            $image->stripImage();
        }
        else
        {
            // COLORSPACE MANAGEMENT NOT AVAILABLE IN GD!
        }
    }

}
