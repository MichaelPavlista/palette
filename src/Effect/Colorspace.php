<?php

namespace Palette\Effect;

use Palette\Picture;
use Imagick;

/**
 * Class Resize
 * @package Effect
 */
class Colorspace extends PictureEffect {


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    public function apply(Picture $picture) {

        $image = $picture->getResource(Picture::WORKER_IMAGICK);

        if($image->getImageColorspace() == Imagick::COLORSPACE_CMYK) {

            $profiles = $image->getImageProfiles('*', FALSE);

            $path = realpath(__DIR__ . '/../Profiles/') . DIRECTORY_SEPARATOR;

            // POKUD NEMÁME CMYK ICC PROFIL PØIDÁME HO
            if(array_search('icc', $profiles) === false) {

                $image->profileImage('icc', file_get_contents($path . 'USWebUncoated.icc'));
            }

            // PØIDÁNÍ RGB PROFILU
            $image->profileImage('icc', file_get_contents($path . 'sRGB _Color_Space_Profile.icm'));
        }

        $image->stripImage();
    }

}