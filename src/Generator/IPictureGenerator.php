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

namespace Palette\Generator;

use Palette\Picture;
use Palette\Exception;

/**
 * Interface IPictureGenerator
 * @package Palette\Generator
 */
interface IPictureGenerator {

    /**
     * Get picture instance for transformation performs by this picture generator.
     * @param string $image path to image file
     * @param string|null $worker Palette\Picture worker constant
     * @return Picture
     */
    public function loadPicture($image, $worker = NULL);


    /**
     * Set picture loader witch can modify or change loaded picture
     * @param IPictureLoader $pictureLoader
     * @throws Exception
     */
    public function setPictureLoader(IPictureLoader $pictureLoader);


    /**
     * Save picture variant to generator storage.
     * @param Picture $picture
     */
    public function save(Picture $picture);


    /**
     * Remove picture variant from generator storage.
     * @param Picture $picture
     * @param bool $otherVariants remove also other variants of image?
     * @return bool
     */
    public function remove(Picture $picture, $otherVariants = FALSE);


    /**
     * Returns file path of the image file variant.
     * Does't verify if the file physically exists.
     * @param Picture $picture
     * @return string|bool
     */
    public function getPath(Picture $picture);


    /**
     * Returns the absolute URL of the image to the desired variant.
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture);


    /**
     * Set fallback image witch is used when requred image is not found.
     * @param string $fallbackImage absolute or relative path to fallback image.
     * @throws Exception
     */
    public function setFallbackImage($fallbackImage);


    /**
     * Get fallback image witch is used when requred image is not found.
     * @return string|null
     */
    public function getFallbackImage();

    
    /**
     * Set image query template
     * @param string $template
     * @param string $imageQuery
     */
    public function setTemplateQuery($template, $imageQuery);


    /**
     * Get defined template image query
     * @param string $template
     * @return string|bool
     */
    public function getTemplateQuery($template);

}