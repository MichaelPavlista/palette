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

/**
 * Class Compare
 * Tool for comparing two image files
 * @package Palette\Utils
 */
class Compare {

    /**
     * @var string path to image
     */
    protected $image1;

    /**
     * @var string path to image
     */
    protected $image2;

    /**
     * @var int number of different pixels
     */
    protected $differentPixels = 0;


    /**
     * Compare constructor.
     * @param string $image1 path to first image
     * @param string $image2 path to second image
     * @throws Exception
     */
    public function __construct($image1, $image2) {

        // LOAD FIRST IMAGE
        if(file_exists($image1) && is_readable($image1)) {

            $this->image1 = $this->loadImage($image1);
        }
        else {

            throw new Exception('First image to compare is missing or not readable');
        }

        // LOAD SECOND IMAGE
        if(file_exists($image2) && is_readable($image2)) {

            $this->image2 = $this->loadImage($image2);
        }
        else {

            throw new Exception('Second image to compare is missing or not readable');
        }
    }


    /**
     * Load image into GD resource.
     * @param string $imagePath path to image
     * @return resource GD
     * @throws Exception
     */
    protected function loadImage($imagePath) {

        $image = @imagecreatefromstring(file_get_contents($imagePath));

        if(!$image) {

            throw new Exception('File ' . $imagePath . ' is not valid image.');
        }

        return $image;
    }


    /**
     * Check if dimensions of images is equal.
     * @return bool
     */
    public function isDimensionsEqual() {

        if(imagesx($this->image1) !== imagesx($this->image2) || imagesy($this->image1) !== imagesy($this->image2)) {

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

        // CHECK IF IMAGES IS EQUAL PIXEL TO PIXEL
        $this->differentPixels = 0;

        $sx1 = imagesx($this->image1);
        $sy1 = imagesy($this->image1);

        for($x = 0; $x < $sx1; $x++) {

            for($y = 0; $y < $sy1; $y++) {

                $index1 = imagecolorat($this->image1, $x, $y);
                $pixel1 = imagecolorsforindex($this->image1, $index1);

                $index2 = imagecolorat($this->image2, $x, $y);
                $pixel2 = imagecolorsforindex($this->image2, $index2);

                if($pixel1 !== $pixel2) {

                    $this->differentPixels++;
                }
            }
        }

        return !$this->differentPixels;
    }

}