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

namespace Palette;

use Palette\Generator\IPictureGenerator;
use ReflectionClass;
use Palette\Effect\Colorspace;
use Palette\Effect\PictureEffect;
use Palette\Effect\Resize;
use Imagick;

/**
 * Class Picture
 */
class Picture
{
    /** @const picture generation worker (Imagick extension) */
    const WORKER_IMAGICK = 'imagick';

    /** @const picture generation worker (GD extension) */
    const WORKER_GD = 'gd';

    /** @var string picture generation worker */
    protected $worker;

    /** @var string path to source image file */
    protected $image;

    /** @var int image compress quality */
    protected $quality = 90;

    /** @var null|bool save as progressive image? */
    protected $progressive = TRUE;

    /** @var bool use WebP format */
    protected $webp = FALSE;

    /** @var array applied effects on picture */
    protected $effect = array();

    /** @var Imagick|resource */
    private $resource;

    /** @var null|IPictureGenerator picture generator instance */
    protected $storage = NULL;

    /** @var array generated temp files */
    protected $tmpImage = array();


    /**
     * Picture constructor.
     * @param string $image
     * @param IPictureGenerator|NULL $pictureGenerator
     * @param null $worker worker constant
     * @param null|string $fallbackImage absolute path to image witch can be used when source image is missing
     * @throws Exception
     */
    public function __construct($image, IPictureGenerator $pictureGenerator = NULL, $worker = NULL, $fallbackImage = NULL)
    {
        // LOAD PICTURE FALLBACK IMAGE FROM GENERATOR
        if(!$fallbackImage && $pictureGenerator)
        {
            $fallbackImage = $pictureGenerator->getFallbackImage();
        }

        // SUPPORT FOR PALETTE IMAGE QUERY
        if(strpos($image, '@'))
        {
            $imageParts  = explode('@', $image);
            $this->image = realpath($imageParts[0]);

            // EXTRACTION OF IMAGE QUERY
            $imageParts[1] = preg_replace('/\s+/', '', $imageParts[1]);

            // Find and replace templates in image query
            if(preg_match_all('/\.([a-zA-Z][a-zA-Z0-9_-]+)/', $imageParts[1], $templateMatches))
            {
                // Check if picture generator is set.
                if(!$pictureGenerator)
                {
                    throw new Exception(sprintf('Using pallete query template %s without defined generator is unsupported', $imageParts[1]));
                }

                // Replace image query templates
                foreach ($templateMatches[0] as $templateMatch)
                {
                    $templateQuery = $pictureGenerator->getTemplateQuery(mb_substr($templateMatch, 1));

                    if(!$templateQuery)
                    {
                        throw new Exception(sprintf('Trying to use undefined pallete query template %s', $templateMatch));
                    }

                    $imageParts[1] = str_replace($templateMatch, $templateQuery, $imageParts[1]);
                }
            }

            foreach(preg_split("/[\s&|]+/", $imageParts[1]) as $effect)
            {
                if(preg_match('#^[0-9]#', $effect))
                {
                    $effectClass = 'Palette\Effect\Resize';
                    $effectQuery = explode(';', $effect);
                }
                else
                {
                    $effectQuery = explode(';', $effect);
                    $effectClass = "Palette\\Effect\\" . ucfirst($effectQuery[0]);

                    unset($effectQuery[0]);

                    // SUPPORT QUALITY ARGUMENT IN PALETTE QUERY
                    if($effectClass === 'Palette\Effect\Quality' && isset($effectQuery[1]))
                    {
                        $this->quality($effectQuery[1]);

                        continue;
                    }

                    // SUPPORT WEBP ARGUMENT IN PALETTE QUERY
                    if($effectClass === 'Palette\Effect\WebP')
                    {
                        $this->webp = TRUE;

                        continue;
                    }

                    // SUPPORT PROGRESSIVE IMAGE SAVING ARGUMENT IN PALETTE QUERY
                    if($effectClass === 'Palette\Effect\Progressive')
                    {
                        $this->progressive = TRUE;
                    }
                }

                if(class_exists($effectClass))
                {
                    $reflection  = new ReflectionClass($effectClass);
                    $queryEffect = $reflection->newInstanceArgs($effectQuery);
                    $queryEffect->restore($effectQuery);

                    $this->effect($queryEffect);
                }
            }
        }
        else
        {
            $this->image = realpath($image);
        }

        // CHECK IF IMAGE EXISTS AND IS READABLE
        if(!file_exists($this->image) || !is_readable($this->image) || !is_file($this->image))
        {
            // USE FALLBACK IMAGE INSTEAD
            if($fallbackImage && file_exists($fallbackImage) && is_readable($fallbackImage))
            {
                $this->image = $fallbackImage;
            }
            else
            {
                throw new Exception("Image file missing or not readable, query: $image");
            }
        }

        $this->storage = $pictureGenerator;
        $this->worker  = $worker;
    }


    /**
     * Create safe imagick instance, solve Imagick segmentation fault bug
     * https://bugs.php.net/bug.php?id=61122
     * @param mixed $files The path to an image to load or an array of paths. Paths can include
     * wildcards for file names, or can be URLs.
     * @return Imagick
     */
    public function createImagick($files = NULL)
    {
        $imagick = new Imagick($files);
        $imagick->setResourceLimit(6, 1);

        return $imagick;
    }


    /**
     * Return path to source image file
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * Is PHP extension Imagick available?
     * @return bool
     */
    public static function imagickAvailable()
    {
        return extension_loaded('imagick');
    }


    /**
     * Is PHP extension GD available?
     * @return bool
     */
    public static function gdAvailable()
    {
        return extension_loaded('gd');
    }


    /**
     * Loads an image as a resource for applying effects and transformations
     * @throws Exception
     */
    protected function loadImageResource()
    {
        if(empty($this->resource))
        {
            if(($this->worker === NULL || $this->worker === $this::WORKER_IMAGICK) && self::imagickAvailable())
            {
                $this->resource = $this->createImagick($this->image);
            }
            elseif(($this->worker === NULL || $this->worker === $this::WORKER_GD) && self::gdAvailable())
            {
                $this->resource = $this->getGdResource($this->image);
            }
            else
            {
                throw new Exception('Required extensions missing, extension GD or Imagick is required');
            }
        }
    }


    /**
     * Modifies this image through Imagick?
     * @return bool
     */
    public function isImagick()
    {
        return is_object($this->resource) && get_class($this->resource) === 'Imagick';
    }


    /**
     * Modifies this image through GD?
     * @return bool
     */
    public function isGd()
    {
        return is_resource($this->resource);
    }


    /**
     * Get resource of picture in specified format (GD resource / Imagick instance).
     * @param null $worker worker constant
     * @return Imagick|resource
     */
    public function getResource($worker = NULL)
    {
        $this->loadImageResource();

        if(is_null($worker))
        {
            $worker = $this->isImagick() ? $this::WORKER_IMAGICK : $this::WORKER_GD;
        }

        if(($worker === $this::WORKER_IMAGICK && $this->isImagick()) || ($worker === $this::WORKER_GD && $this->isGd()))
        {
            return $this->resource;
        }
        else
        {
            return $this->convertResource($worker);
        }
    }


    /**
     * Performs conversion between GD and Imagick resource instances.
     * @param string $convertTo worker constant
     * @return Imagick|resource
     * @throws Exception
     */
    protected function convertResource($convertTo)
    {
        $this->tmpImage[] = $tmpImage = tempnam(sys_get_temp_dir(), 'picture');

        if($this->isGd())
        {
            imagepng($this->resource, $tmpImage, 0);
        }
        else
        {
            $this->resource->writeImage($tmpImage);
        }

        if($convertTo === $this::WORKER_GD)
        {
            return $this->getGdResource($tmpImage);
        }
        else
        {
            return $this->createImagick($tmpImage);
        }
    }


    /**
     * Find used worker for specified image source (GD vs Imagick)
     * @param Imagick|resource $resource
     * @return string worker constant
     */
    protected function getWorker($resource)
    {
        return is_resource($resource) ? self::WORKER_GD : self::WORKER_IMAGICK;
    }


    /**
     * Sets the resource picture to another
     * @param Imagick|resource $resource
     */
    public function setResource($resource)
    {
        if($this->getWorker($resource) === $this->getWorker($this->resource))
        {
            $this->resource = $resource;
        }

        $this->tmpImage[] = $tmpImage = tempnam(sys_get_temp_dir(), 'picture');

        if($this->getWorker($resource) === $this::WORKER_GD)
        {
            imagepng($resource, $tmpImage, 0);
        }
        else
        {
            $resource->writeImage($tmpImage);
        }

        if($this->isGd())
        {
            $this->resource = $this->getGdResource($tmpImage);
        }
        else
        {
            $this->resource = $this->createImagick($tmpImage);
        }
    }


    /**
     * Normalize GD resource
     * @param $resource
     * @return resource
     */
    protected function normalizeGdResource($resource)
    {
        if(imageistruecolor($resource))
        {
            imagesavealpha($resource, TRUE);

            return $resource;
        }

        $width  = imagesx($resource);
        $height = imagesy($resource);

        $trueColor = imagecreatetruecolor($width, $height);

        imagealphablending($trueColor, FALSE);

        $transparent = imagecolorallocatealpha($trueColor, 255, 255, 255, 127);

        imagefilledrectangle($trueColor, 0, 0, $width, $height, $transparent);
        imagealphablending($trueColor, TRUE);

        imagecopy($trueColor, $resource, 0, 0, 0, 0, $width, $height);
        imagedestroy($resource);

        imagesavealpha($trueColor, TRUE);

        return $trueColor;
    }


    /**
     * Creating a resource of GD image file
     * @param string $imageFile image file path
     * @return resource
     * @throws Exception
     */
    protected function getGdResource($imageFile)
    {
        $imageInfo = getimagesize($imageFile);

        switch($imageInfo['mime'])
        {
            case 'image/jpg':
            case 'image/jpeg':
                return $this->normalizeGdResource(imagecreatefromjpeg($imageFile));

            case 'image/gif':
                return $this->normalizeGdResource(imagecreatefromgif($imageFile));

            case 'image/png':
                return $this->normalizeGdResource(imagecreatefrompng($imageFile));

            case 'image/webp':
                return $this->normalizeGdResource(imagecreatefromwebp($imageFile));
        }

        throw new Exception('GD resource not supported image extension');
    }


    /**
     * @param $resource
     * @return bool
     */
    protected function isGdImageTransparent($resource)
    {
        $width  = imagesx($resource);
        $height = imagesy($resource);

        for($i = 0; $i < $width; $i++)
        {
            for($j = 0; $j < $height; $j++)
            {
                $rgba = imagecolorat($resource, $i, $j);

                if(($rgba & 0x7F000000) >> 24)
                {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }


    /**
     * Get image query string to create image with the actual effects
     * @return string
     */
    public function getImageQuery()
    {
        $command = '';

        foreach($this->effect as $effect)
        {
            if($effect instanceof PictureEffect)
            {
                $command .= $effect->__toString() . '&';
            }
        }

        $command .= 'Quality;' . $this->quality . '&';

        if($this->webp)
        {
            $command .= 'WebP&';
        }

        if($this->progressive)
        {
            $command .= 'Progressive&';
        }

        return substr($command, 0, strlen($command) - 1);
    }


    /**
     * Add new picture effect
     * @param $effect
     * @throws Exception
     */
    public function effect($effect)
    {
        $args = array_slice(func_get_args(), 1);

        if($effect instanceof PictureEffect)
        {
            $this->effect[] = $effect;
        }
        else
        {
            $effectClass = "Palette\\Effect\\" . $effect;

            if(class_exists($effectClass))
            {
                $reflection = new ReflectionClass($effectClass);
                $this->effect[] = $reflection->newInstanceArgs($args);
            }
            else
            {
                throw new Exception('Unknown Palette effect instance');
            }
        }
    }


    /**
     * Resize picture by specified dimensions
     * @param int $width
     * @param int $height
     * @param null $resizeMode (Palette\Effect\Resize constant)
     */
    public function resize($width, $height = NULL, $resizeMode = NULL)
    {
        if(!$height)
        {
            $height = $width;
        }

        $this->effect[] = new Resize($width, $height, $resizeMode);
    }


    /**
     * Set picture output quality 1 - 100
     * @param int $quality
     */
    public function quality($quality = 100)
    {
        if(is_numeric($quality))
        {
            $this->quality = $quality;
        }
        else
        {
            trigger_error('Picture quality must be numeric', E_USER_WARNING);
        }
    }


    /**
     * Set picture saving mode as progressive image?
     * @param bool $progressive
     */
    public function setProgressive($progressive = TRUE)
    {
        if(is_bool($progressive))
        {
            $this->progressive = $progressive;
        }
        else
        {
            trigger_error('Picture progressive mode must be boolean', E_USER_WARNING);
        }
    }


    /**
     * Save the edited image to the repository, or specific location
     * @param null $file
     */
    public function save($file = NULL)
    {
        if($file === NULL && $this->storage)
        {
            $this->storage->save($this);
        }
        elseif($file !== NULL)
        {
            $this->savePicture($file);
        }
        else
        {
            trigger_error('Image file to save is not defined', E_USER_WARNING);
        }
    }


    /**
     * Generate image from a source and save to specified file
     * @param $file
     */
    protected function savePicture($file)
    {
        $this->loadImageResource();

        // SUPPORT FOR CMYK IMAGES
        $colorSpace = new Colorspace();
        $colorSpace->apply($this);

        // APPLY EFFECT ON IMAGE
        foreach($this->effect as $effect)
        {
            if($effect instanceof PictureEffect)
            {
                $effect->apply($this);
            }
        }

        // CREATE DIRECTORY BY FILE PATH
        if(!file_exists($directory = dirname($file)))
        {
            mkdir($directory, 0777, TRUE);
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if($this->isGd())
        {
            if($extension === 'webp' || $this->isWebp())
            {
                if($this->progressive)
                {
                    imageinterlace($this->resource, TRUE);
                }

                imagewebp($this->resource, $file, $this->quality);
            }
            elseif($extension === 'png')
            {
                if($this->progressive)
                {
                    imageinterlace($this->resource, TRUE);
                }

                imagepng($this->resource, $file, 9);
            }
            elseif($extension === 'gif')
            {
                $gifResource = $this->resource;

                if($this->isGdImageTransparent($gifResource))
                {
                    $validAlpha = ceil(0.333 * 127);
                    $visiblePixels = 0;

                    $height = imagesy($gifResource);
                    $width  = imagesx($gifResource);

                    $transparentColor = imagecolorallocate($gifResource, 0xfe, 0x3, 0xf4);

                    // FIX GIF IMAGE OPACITY
                    for($x = 0; $x < $width; $x++)
                    {
                        for($y = 0; $y < $height; $y++)
                        {
                            $pixelIndex = imagecolorat($gifResource, $x, $y);
                            $pixelColor = imagecolorsforindex($gifResource, $pixelIndex);

                            if($pixelColor['alpha'] <= $validAlpha)
                            {
                                $visiblePixels++;
                            }
                        }
                    }

                    if(!$visiblePixels)
                    {
                        $validAlpha = 127;
                    }

                    for($x = 0; $x < $width; $x++)
                    {
                        for($y = 0; $y < $height; $y++)
                        {
                            $pixelIndex = imagecolorat($gifResource, $x, $y);
                            $pixelColor = imagecolorsforindex($gifResource, $pixelIndex);

                            if($pixelColor['alpha'] >= $validAlpha)
                            {
                                imagesetpixel($gifResource, $x, $y, $transparentColor);
                            }
                            else
                            {
                                $visiblePixel = imagecolorallocatealpha(

                                    $gifResource,
                                    $pixelColor['red'],
                                    $pixelColor['green'],
                                    $pixelColor['blue'],
                                    0
                                );

                                imagesetpixel($gifResource, $x, $y, $visiblePixel);
                            }
                        }
                    }

                    imagecolortransparent($gifResource, $transparentColor);
                }

                if($this->progressive)
                {
                    imageinterlace($gifResource, TRUE);
                }

                imagegif($gifResource, $file);
            }
            else
            {
                $image = $this->resource;

                $width  = imagesx($image);
                $height = imagesy($image);

                $background = imagecreatetruecolor($width, $height);
                $whiteColor = imagecolorallocate($background,  255, 255, 255);

                imagefilledrectangle($background, 0, 0, $width, $height, $whiteColor);
                imagecopy($background, $image, 0, 0, 0, 0, $width, $height);

                if($this->progressive)
                {
                    imageinterlace($background, TRUE);
                }

                imagejpeg($background, $file, $this->quality);
            }
        }
        else
        {
            if($extension === 'webp' || $this->isWebp())
            {
                try
                {
                    if($this->progressive)
                    {
                        $this->resource->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                    }
                    $this->resource->setImageFormat('webp');

                    $this->resource->writeImage($file);
                }
                catch (\ImagickException $e)
                {
                    // Falback to save webp image
                    if($e->getMessage() === 'Unable to set image format')
                    {
                        $imageGd = $this->convertResource($this::WORKER_GD);

                        if($this->progressive)
                        {
                            imageinterlace($imageGd, TRUE);
                        }

                        imagewebp($imageGd, $file, $this->quality);
                    }
                }
            }
            elseif($extension === 'jpg')
            {
                $background = $this->createImagick();
                $background->newImage(

                    $this->resource->getImageWidth(),
                    $this->resource->getImageHeight(),
                    '#FFFFFF'
                );

                $background->compositeImage($this->resource, Imagick::COMPOSITE_OVER, 0, 0);
                $background->setImageFormat('jpg');
                $background->setImageCompression(Imagick::COMPRESSION_JPEG);
                $background->setImageCompressionQuality($this->quality);

                if($this->progressive)
                {
                    $background->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                }

                $background->writeImage($file);
            }
            elseif($extension === 'gif')
            {
                $validAlpha = 0.333;
                $visiblePixels = 0;
                $iterator = $this->resource->getPixelIterator();

                /** @var $pixel \ImagickPixel */
                foreach($iterator as $row => $pixels)
                {
                    foreach ($pixels as $col => $pixel)
                    {
                        $color = $pixel->getColor(TRUE);

                        if($color['a'] >= $validAlpha)
                        {
                            $visiblePixels++;
                        }
                    }

                    $iterator->syncIterator();
                }

                if(!$visiblePixels)
                {
                    $validAlpha = 0;
                }

                // SAVE NEW ALPHA COLOR VALUE
                foreach($iterator as $row => $pixels)
                {
                    foreach ($pixels as $col => $pixel)
                    {
                        $color = $pixel->getColor(TRUE);
                        $pixel->setColorValue(Imagick::COLOR_ALPHA, $color['a'] >= $validAlpha ? 1 : 0);
                    }

                    $iterator->syncIterator();
                }

                if($this->progressive)
                {
                    $this->resource->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                }

                $this->resource->writeImage($file);
            }
            else
            {
                if($this->progressive)
                {
                    $this->resource->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                }

                $this->resource->writeImage($file);
            }
        }

        chmod($file, 0777);
        touch($file, filemtime($this->getImage()));

        // DELETE TEMP FILES
        foreach($this->tmpImage as $tmpImage)
        {
            unlink($tmpImage);
        }
    }


    /**
     * Output the image to the browser
     * @return void
     * @exit
     */
    public function output()
    {
        $imageFile = $this->storage->getPath($this);

        if(file_exists($imageFile))
        {
            header('Content-Type: image');
            header('Content-Length: ' . filesize($imageFile));

            readfile($imageFile);
            exit;
        }
    }


    /**
     * Returns the URL of the image, if is needed save this image
     * @return null|string
     */
    public function getUrl()
    {
        if($this->storage)
        {
            $this->save();

            return $this->storage->getUrl($this);
        }

        return NULL;
    }


    /**
     * Getting new dimensions for applying effects to an image, the image does not save
     * @return array w,h
     */
    public function getDimensions()
    {
        $imageDimension = getimagesize($this->image);

        $width  = $imageDimension[0];
        $height = $imageDimension[1];

        foreach($this->effect as $effect)
        {
            if($effect instanceof PictureEffect)
            {
                $modified = $effect->getNewDimensions($width, $height);

                $width  = $modified['w'];
                $height = $modified['h'];
            }
        }

        return array(

            'w' => $width,
            'h' => $height,
        );
    }


    /**
     * Mají se vždy verze obrázku uložit jako WebP?
     * @param bool $forceWebP
     */
    public function forceWebP($forceWebP = TRUE)
    {
        $this->webp = (bool) $forceWebP;
    }


    /**
     * Má se obrázek uložit jako Webp?
     * @return bool
     */
    public function isWebp()
    {
        return $this->webp;
    }
}
