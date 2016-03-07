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

use Palette\Exception;
use Palette\Picture;

/**
 * Class CurrentExecution
 * Implementation of IPictureGenerator which generates the desired image variants at runtime the current PHP script.
 * @package Palette\Generator
 */
class CurrentExecution implements IPictureGenerator {

    /**
     * @var string absolute path to directory for storage generated image variants
     */
    protected $storagePath;

    /**
     * @var string absolute url to directory of generated images
     */
    protected $storageUrl;

    /**
     * Path to website directory root (see documentation)
     * @var string|null
     */
    protected $basePath;


    /**
     * CurrentExecution constructor.
     * @param string $storagePath absolute or relative path to directory for storage generated image variants
     * @param string $storageUrl absolute url to directory of generated images
     * @param string|null $basePath path to website directory root (see documentation)
     * @throws Exception
     */
    public function __construct($storagePath, $storageUrl, $basePath = NULL) {

        $storagePath = realpath($storagePath);

        if(!file_exists($storagePath)) {

            throw new Exception('Image storagePath does not exists');
        }
        elseif(!is_writable($storagePath)) {

            throw new Exception("Image storagePath '$storagePath' is not writable");
        }

        $this->storagePath = $storagePath;
        $this->storageUrl = $storageUrl;
        $this->basePath = $basePath;
    }


    /**
     * Get picture instance for transformation performs by this picture generator.
     * @param string $image path to image file
     * @param string|null $worker
     * @return Picture
     */
    public function loadPicture($image, $worker = NULL) {

        return new Picture($image, $this, $worker);
    }


    /**
     * Save picture variant to generator storage.
     * @param Picture $picture
     * @return void
     */
    public function save(Picture $picture) {

        $pictureFile = $this->getFile($picture);

        if(!$this->isFileActual($pictureFile, $picture)) {

            $picture->save($pictureFile);
        }
    }


    /**
     * Remove picture variant from generator storage.
     * @param Picture $picture
     * @param bool $otherVariants remove also other variants of image?
     * @return bool
     */
    public function remove(Picture $picture, $otherVariants = FALSE) {

        return FALSE;
    }


    /**
     * Returns file path of the image file variant.
     * Does't verify if the file physically exists.
     * @param Picture $picture
     * @return string
     */
    public function getPath(Picture $picture) {

        return $this->storagePath . '/' . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Returns the absolute URL of the image to the desired variant.
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture) {

        return $this->storageUrl . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Check if picture variant exists and is actual
     * @param string $file
     * @param Picture $picture
     * @return bool|null
     */
    protected function isFileActual($file, Picture $picture) {

        if(file_exists($file)) {

            if(@filemtime($file) === @filemtime($picture->getImage())) {

                return TRUE;
            }
            else {

                return NULL;
            }
        }

        return FALSE;
    }


    /**
     * Returns specified picture variant basename (file name)
     * @param Picture $picture
     * @return string
     */
    protected function getFileName(Picture $picture) {

        $imageFile = $picture->getImage();

        return pathinfo($imageFile, PATHINFO_FILENAME) . '.' .
        sprintf("%u", crc32($picture->getImageQuery())) . '.' . pathinfo($imageFile, PATHINFO_EXTENSION);
    }

}