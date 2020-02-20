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
class CurrentExecution implements IPictureGenerator
{
    /** @var string absolute path to directory for storage generated image variants */
    protected $storagePath;

    /** @var string absolute url to directory of generated images */
    protected $storageUrl;

    /** @var string|null Path to website directory root (see documentation) */
    protected $basePath;

    /** @var array palette query templates storage */
    protected $template = array();

    /** @var string|null absolute path to fallback image */
    protected $fallbackImage;

    /** @var IPictureLoader witch can modify or change loaded picture */
    protected $pictureLoader;

    /** @var int default quality */
    protected $defaultQuality;


    /**
     * CurrentExecution constructor.
     * @param string $storagePath absolute or relative path to directory for storage generated image variants
     * @param string $storageUrl absolute url to directory of generated images
     * @param string|null $basePath path to website directory root (see documentation)
     * @throws Exception
     */
    public function __construct($storagePath, $storageUrl, $basePath = NULL)
    {
        $storagePath = realpath($storagePath);

        if (!file_exists($storagePath))
        {
            throw new Exception('Image storagePath does not exists');
        }

        if(!is_writable($storagePath))
        {
            throw new Exception("Image storagePath '$storagePath' is not writable");
        }

        $this->storagePath = $this->unifyPath($storagePath);
        $this->storageUrl = $storageUrl;
        $this->basePath = $this->unifyPath($basePath);
    }


    /**
     * Get picture instance for transformation performs by this picture generator.
     * @param string $image path to image file
     * @param string|null $worker Palette\Picture worker constant
     * @return Picture
     * @throws Exception
     */
    public function loadPicture($image, $worker = NULL)
    {
        if($this->pictureLoader)
        {
            return $this->pictureLoader->loadPicture($image, $this, $worker);
        }

        return new Picture($image, $this, $worker, $this->fallbackImage);
    }


    /**
     * Set picture loader witch can modify or change loaded picture
     * @param IPictureLoader $pictureLoader
     * @throws Exception
     */
    public function setPictureLoader(IPictureLoader $pictureLoader)
    {
        $this->pictureLoader = $pictureLoader;
    }


    /**
     * Save picture variant to generator storage.
     * @param Picture $picture
     * @return void
     */
    public function save(Picture $picture)
    {
        $pictureFile = $this->getPath($picture);

        if(!$this->isFileActual($pictureFile, $picture))
        {
            $picture->save($pictureFile);
        }
    }


    /**
     * Remove picture variant from generator storage.
     * @param Picture $picture
     * @param bool $otherVariants remove also other variants of image?
     * @return bool
     */
    public function remove(Picture $picture, $otherVariants = FALSE)
    {
        return FALSE;
    }


    /**
     * Returns file path of the image file variant.
     * Does't verify if the file physically exists.
     * @param Picture $picture
     * @return string
     */
    public function getPath(Picture $picture)
    {
        return $this->storagePath . '/' . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Returns the absolute URL of the image to the desired variant.
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture)
    {
        return $this->storageUrl . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Check if picture variant exists and is actual
     * @param string $file
     * @param Picture $picture
     * @return bool|null
     */
    protected function isFileActual($file, Picture $picture)
    {
        if(file_exists($file))
        {
            if(@filemtime($file) === @filemtime($picture->getImage()))
            {
                return TRUE;
            }
            else
            {
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
    public function getFileName(Picture $picture)
    {
        $sourceImage = $picture->getImage();

        $fileName = pathinfo($sourceImage, PATHINFO_FILENAME) . '.' .
            sprintf('%u', crc32($picture->getImageQuery())) . '.';

        if($created = filemtime($sourceImage))
        {
            $fileName .= $created . '.';
        }

        // Create picture extension.
        $pictureExtension = pathinfo($sourceImage, PATHINFO_EXTENSION);

        if($picture->isWebp() && strtolower($pictureExtension) !== 'webp')
        {
            $pictureExtension .= '.webp';
        }

        return $fileName . $pictureExtension;
    }


    /**
     * Set fallback image witch is used when requred image is not found.
     * @param string $fallbackImage absolute or relative path to fallback image.
     * @throws Exception
     */
    public function setFallbackImage($fallbackImage)
    {
        $fallbackImagePath = realpath($fallbackImage);

        if(file_exists($fallbackImagePath) && is_readable($fallbackImagePath))
        {
            $this->fallbackImage = $fallbackImagePath;

            return;
        }

        throw new Exception("Default image missing or not readable, path: $fallbackImage");
    }


    /**
     * Get fallback image witch is used when requred image is not found.
     * @return string|null
     */
    public function getFallbackImage()
    {
        return $this->fallbackImage;
    }


    /**
     * Set image query template
     * @param string $template
     * @param string $imageQuery
     * @return void
     */
    public function setTemplateQuery($template, $imageQuery)
    {
        if(!is_string($template) || !preg_match('/^[a-zA-Z0-9_-]+$/', $template))
        {
            throw new Exception('Palette template name must match expression ^[a-zA-Z0-9_-]+$');
        }

        $this->template[$template] = $imageQuery;
    }


    /**
     * Set default image quality
     * @param $quality
     */
    public function setDefaultQuality($quality)
    {
        if (FALSE === is_int($quality) || $quality < 1 || $quality > 100)
        {
            throw new Exception('Palette quality must be between 1-100');
        }

        $this->defaultQuality = $quality;
    }


    /**
     * Get default image quality
     * @return int|null
     */
    public function getDefaultQuality()
    {
        return $this->defaultQuality;
    }


    /**
     * Get defined template image query
     * @param string $template
     * @return string|bool
     */
    public function getTemplateQuery($template)
    {
        if(isset($this->template[$template]))
        {
            return $this->template[$template];
        }

        return FALSE;
    }

    /**
     * Unify filesystem path
     * @param string $path
     * @param string $slash
     * @return string
     */
    protected function unifyPath($path, $slash = DIRECTORY_SEPARATOR)
    {
        return preg_replace('/\\'. $slash .'+/', $slash, str_replace(array('/', "\\"), $slash, $path));
    }

}
