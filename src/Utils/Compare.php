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

namespace Palette\Utils;

use Palette\Exception;
use Imagick;

/**
 * Class Compare
 * Tool for comparing two image files
 * @package Palette\Utils
 */
class Compare {

    /**
     * @var Imagick
     */
    protected $image1;

    /**
     * @var Imagick
     */
    protected $image2;

    /**
     * @var float
     */
    protected $tolerance;


    /**
     * Compare constructor.
     * @param $image1
     * @param $image2
     * @param float $tolerance
     * @throws Exception
     */
    public function __construct($image1, $image2, $tolerance = 0.005) {

        $this->tolerance = $tolerance;

        // LOAD FIRST IMAGE
        if(file_exists($image1) && is_readable($image1)) {

            $this->image1 = new Imagick($image1);
        }
        else {

            throw new Exception('First image to compare is missing or not readable');
        }

        // LOAD SECOND IMAGE
        if(file_exists($image2) && is_readable($image2)) {

            $this->image2 = new Imagick($image2);
        }
        else {

            throw new Exception('Second image to compare is missing or not readable');
        }
    }


    /**
     * Check if dimensions of images is equal.
     * @return bool
     */
    public function isDimensionsEqual() {

        if($this->image1->getImageWidth() !== $this->image2->getImageWidth() ||
            $this->image1->getImageHeight() !== $this->image2->getImageHeight()) {

            return FALSE;
        }

        return TRUE;
    }


    /**
     * Check if images is exactly the same.
     * @return bool
     */
    public function isEqual() {

        // CHECK IMAGES DIMENSIONS
        if(!$this->isDimensionsEqual()) {

            return FALSE;
        }

        $compare = $this->image1->compareImages($this->image2, Imagick::METRIC_MEANSQUAREERROR);

        // IMAGE IS EXACTLY THE SAME
        if($compare === TRUE) {

            return TRUE;
        }
        // IMAGE IS IN TOLERABLE RANGE
        elseif($compare) {

            var_dump($compare[1]);

            if($compare[1] < $this->tolerance) {

                return TRUE;
            }
        }

        return FALSE;
    }

}