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
use Imagick;
use Palette\Effect\Colorspace;
use Palette\Effect\PictureEffect;
use Palette\Effect\Resize;

/**
 * Class Picture
 */
class Picture {

    /**
     * @const worker
     */
    const WORKER_IMAGICK = 'imagick';

    /**
     * @const worker
     */
    const WORKER_GD = 'gd';

    /**
     * @var string worker
     */
    protected $worker;

    /**
     * @var string cesta k obrázku
     */
    protected $image;

    /**
     * @var int kvalita obrázku
     */
    protected $quality = 100;

    /**
     * @var array aplikované efekty
     */
    protected $effect = array();

    /**
     * @var Imagick|resource
     */
    private $resource;

    /**
     * @var null|IPictureGenerator
     */
    protected $storage = NULL;

    /**
     * @var array
     */
    protected $tmpImage = array();


    /**
     * Picture constructor.
     * @param $image
     * @param IPictureGenerator|NULL $pictureStorage
     * @param null $worker
     * @throws Exception
     */
    public function __construct($image, IPictureGenerator $pictureStorage = NULL, $worker = NULL) {

        // PODPORA PRO IMAGE QUERY
        if(strpos($image, '@')) {

            $imageParts  = explode('@', $image);
            $this->image = realpath($imageParts[0]);

            $imageParts[1] = preg_replace('/\s+/', '', $imageParts[1]);

            foreach(preg_split("/[\s&|]+/", $imageParts[1]) as $effect) {

                if(preg_match('#^[0-9]#', $effect)) {

                    $effectClass = 'Palette\Effect\Resize';
                    $effectQuery = explode(';', $effect);
                }
                else {

                    $effectQuery = explode(';', $effect);
                    $effectClass = "Palette\\Effect\\" . ucfirst($effectQuery[0]);

                    unset($effectQuery[0]);
                }

                if(class_exists($effectClass)) {

                    $reflection  = new ReflectionClass($effectClass);
                    $queryEffect = $reflection->newInstanceArgs($effectQuery);
                    $queryEffect->restore($effectQuery);

                    $this->effect($queryEffect);
                }
            }
        }
        else {

            $this->image = realpath($image);
        }

        // OVĚŘENÍ DOSTUPNOSTI SOUBORU OBRÁZKU
        if(!file_exists($this->image) || !is_readable($this->image)) {

            throw new Exception('Image file missing or not readable');
        }

        $this->storage = $pictureStorage;
        $this->worker  = $worker;
    }


    /**
     * Načte obrázek jako resource pro aplikování efektů
     * @throws Exception
     */
    protected function loadImageResource() {

        if(empty($this->resource)) {

            // ULOŽENÍ ZDROJOVÝCH DAT OBRÁZKU PODLE POUŽITÉ KNIHOVNY
            if((is_null($this->worker) || $this->worker === $this::WORKER_IMAGICK) && $this->imagickAvailable()) {

                $this->resource = new Imagick($this->image);
            }
            elseif((is_null($this->worker) || $this->worker === $this::WORKER_GD) && $this->gdAvailable()) {

                $this->resource = $this->getGdResource($this->image);
            }
            else {

                throw new Exception('Required extensions missing');
            }
        }
    }


    /**
     * Vrací cestu ke zdrojovému obrázku
     * @return string
     */
    public function getImage() {

        return $this->image;
    }


    /**
     * Je PHP rozšíření Imagic dostupné?
     * @return bool
     */
    public static function imagickAvailable() {

        return extension_loaded('imagick');
    }


    /**
     * Je PHP rozšíření GD dostupné?
     * @return bool
     */
    public static function gdAvailable() {

        return extension_loaded('gd');
    }


    /**
     * Upravuje se tento obrázek přes Imagick?
     * @return bool
     */
    public function isImagick() {

        return is_object($this->resource) && get_class($this->resource) === 'Imagick';
    }


    /**
     * Upravuje se tento obrázek přes GD?
     * @return bool
     */
    public function isGd() {

        return is_resource($this->resource);
    }


    /**
     * Zjištění workeru zadaného zdroje obrázku (GD vs Imagick)
     * @param $resource
     * @return string
     */
    public static function getWorker($resource) {

        return is_resource($resource) ? self::WORKER_GD : self::WORKER_IMAGICK;
    }


    /**
     * Získání resource obrázku
     * @param null $worker
     * @return Imagick|resource
     */
    public function getResource($worker = NULL) {

        $this->loadImageResource();

        if(is_null($worker)) {

            $worker = $this->isImagick() ? $this::WORKER_IMAGICK : $this::WORKER_GD;
        }

        if(($worker === $this::WORKER_IMAGICK && $this->isImagick()) || ($worker === $this::WORKER_GD && $this->isGd())) {

            return $this->resource;
        }
        else {

            return $this->convertResource($worker);
        }
    }


    /**
     * Provede převod mezi GD resource a instancí Imagick
     * @param $convertTo
     * @return Imagick|resource
     * @throws Exception
     */
    protected function convertResource($convertTo) {

        $this->tmpImage[] = $tmpImage = tempnam(sys_get_temp_dir(), 'picture');

        if($this->isGd()) {

            imagepng($this->resource, $tmpImage, 0);
        }
        else {

            $this->resource->writeImage($tmpImage);
        }

        if($convertTo === $this::WORKER_GD) {

            return $this->getGdResource($tmpImage);
        }
        else {

            return new Imagick($tmpImage);
        }
    }


    /**
     * Nastavení nového resource obrázku
     * @param Imagick|resource $resource
     */
    public function setResource($resource) {

        if($this->getWorker($resource) === $this->getResource($this->resource)) {

            $this->resource = $resource;
        }

        // ZKONVERTOVÁNÍ ZDROJE OBRÁZKU
        $this->tmpImage[] = $tmpImage = tempnam(sys_get_temp_dir(), 'picture');

        if($this->getWorker($resource) === $this::WORKER_GD) {

            imagepng($resource, $tmpImage, 0);
        }
        else {

            $resource->writeImage($tmpImage);
        }

        if($this->isGd()) {

            $this->resource = $this->getGdResource($tmpImage);
        }
        else {

            $this->resource = new Imagick($tmpImage);
        }
    }


    /**
     * Vytvoření GD resource ze souboru obrázku
     * @param $imageFile
     * @return resource
     * @throws Exception
     */
    protected function getGdResource($imageFile) {

        $imageInfo = getimagesize($imageFile);

        switch($imageInfo['mime']) {

            case 'image/jpg':
            case 'image/jpeg':
                return imagecreatefromjpeg($imageFile);

            case 'image/gif':
                return imagecreatefromgif($imageFile);

            case 'image/png':
                return imagecreatefrompng($imageFile);
        }

        throw new Exception('GD resource not supported extension');
    }


    /* =========================== PRÁCE S EFEKTY =========================== */

    /**
     * Získání image query stringu pro vytvoření obrázku s aktuálními efekty
     * @return string
     */
    public function getImageQuery() {

        $command = '';

        foreach($this->effect as $effect) {

            if($effect instanceof PictureEffect) {

                $command .= $effect->__toString() . '&';
            }
        }

        $command .= 'Quality;' . $this->quality . '&';

        return substr($command, 0, strlen($command) - 1);
    }


    /**
     * @param PictureEffect|string $effect
     * @throws Exception
     */
    public function effect($effect) {

        $args = array_slice(func_get_args(), 1);

        if($effect instanceof PictureEffect) {

            $this->effect[] = $effect;
        }
        else {

            $effectClass = "Palette\\Effect\\" . $effect;

            if(class_exists($effectClass)) {

                $reflection = new ReflectionClass($effectClass);
                $this->effect[] = $reflection->newInstanceArgs($args);
            }
            else {

                throw new Exception('Unknown filter');
            }
        }
    }


    /**
     * @param $width
     * @param $height
     * @param null $resizeMode
     */
    public function resize($width, $height = NULL, $resizeMode = NULL) {

        if(!$height) {

            $height = $width;
        }

        $this->effect[] = new Resize($width, $height, $resizeMode);
    }


    /**
     * @param int $quality
     */
    public function quality($quality = 90) {

        if(is_int($quality)) {

            $this->quality = $quality;
        }
    }


    /**
     * Uloží upravený obrázek do úložiště, popřípadě určeného umístění
     * @param null $file
     */
    public function save($file = NULL) {

        if(is_null($file) && $this->storage) {

            $this->storage->save($this);
        }
        elseif(!is_null($file)) {

            $this->savePicture($file);
        }
        else {

            trigger_error('Image file not defined', E_USER_WARNING);
        }
    }


    /**
     * Odešle obrázek do prohlížeče
     */
    public function output() {

        $imageFile = $this->storage->getPath($this);

        if(file_exists($imageFile)) {

            header('Content-Type: image');
            header('Content-Length: ' . filesize($imageFile));
            readfile($imageFile);
            
            exit;
        }
    }


    /**
     * Vrací url adresu k obrázku, pokud obrázek nebyl uložen, tak ho uloží
     * @return null|string
     */
    public function getUrl() {

        if($this->storage) {

            $this->save();

            return $this->storage->getUrl($this);
        }

        return NULL;
    }


    /**
     * Vrací html tag <img /> s aktuálním obrázkem
     * @param null $alt
     * @param null $title
     * @param string $js
     * @return string
     */
    public function getImageHtml($alt = NULL, $title = NULL, $js = '') {

        if(is_null($title) && $alt) {

            $title = $alt;
        }

        return sprintf('<img src="%s" alt="%s" title="%s" %s/>', $this->getUrl(), $alt, $title, $js ? $js . ' ' : '');
    }


    /**
     * Vrací html tag <img /> s aktuálním obrázkem
     * @return string
     */
    public function __toString() {

        return $this->getImageHtml();
    }


    /**
     * Vyrendruje obrázek ze zdroje a uloží
     * @param $file
     */
    protected function savePicture($file) {

        $this->loadImageResource();

        // PODPORA PRO CMYK OBRÁZKY
        $colorspace = new Colorspace();
        $colorspace->apply($this);

        // APLIKOVÁNÍ EFEKTŮ NA OBRÁZEK
        foreach($this->effect as $effect) {

            if($effect instanceof PictureEffect) {

                $effect->apply($this);
            }
        }

        // CREATE DIRECTORY BY FILE PATH
        if(!file_exists($directory = dirname($file))) {

            mkdir($directory, 0777, TRUE);
        }

        if($this->isGd()) {

            imagejpeg($this->resource, $file, 100);
        }
        else {

            $this->resource->writeImage($file);
        }

        chmod($file, 0777);
        touch($file, filemtime($this->getImage()));

        // SMAZÁNÍ DOČASNÝCH SOUBORŮ
        foreach($this->tmpImage as $tmpImage) {

            unlink($tmpImage);
        }
    }


    /**
     * Získání nových rozměrů po aplikování efektů na obrázek
     * @return array
     */
    public function getDimensions() {

        $imageDimension = getimagesize($this->image);

        $width  = $imageDimension[0];
        $height = $imageDimension[1];

        foreach($this->effect as $effect) {

            if($effect instanceof PictureEffect) {

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

}