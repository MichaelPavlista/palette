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
 * Class Contrast
 * @package Palette\Effect
 */
class Contrast extends PictureEffect
{
    /** @var array effect settings */
    protected $settings = array(

        'contrast' => NULL,
    );


    /**
     * Contrast constructor.
     * @param int $contrast
     */
    public function __construct($contrast)
    {
        $this->contrast = (int) $contrast;
    }


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    public function apply(Picture $picture)
    {
        // GD VERSION IS BETTER AND IS PREFERRED
        if($picture->isGd() || $picture->gdAvailable())
        {
            $resource = $picture->getResource($picture::WORKER_GD);

            imagefilter($resource, IMG_FILTER_CONTRAST, $this->contrast * -1);

            $picture->setResource($resource);
        }
        else
        {
            $resource = $picture->getResource($picture::WORKER_IMAGICK);

            $resource->sigmoidalContrastImage(FALSE, $this->contrast / 4, 0);
        }
    }

}
