<?php

namespace Palette\Generator;

use Palette\Picture;

/**
 * Interface IPictureLoader
 * @package Palette\Generator
 */
interface IPictureLoader
{
    /**
     * Allows override picture loading with palette picture generator
     * @param string $imagePath path to image file
     * @param IPictureGenerator $generator
     * @param string|null $worker Palette\Picture worker constant
     * @return Picture
     */
    public function loadPicture($imagePath, IPictureGenerator $generator, $worker);

}
