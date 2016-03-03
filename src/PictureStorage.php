<?php

namespace Palette;

/**
 * Class PictureStorage
 */
class PictureStorage implements IPictureStorage {

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $basePath;



    /**
     * @param $storagePath
     * @param $storageUrl
     * @param null $basePath
     */
    public function __construct($storagePath, $storageUrl, $basePath = NULL) {

        $storagePath = realpath($storagePath);

        if(!file_exists($storagePath) || !is_writable($storagePath)) {

            throw new \Exception('NOT EXISTS');
        }

        $this->basePath = $basePath;
        $this->path = $storagePath;
        $this->url  = $storageUrl;
    }


    /**
     * Získání instance obrázku náležícímu do tohoto úložiště
     * @param string $image cesta k obrázku
     * @param null $worker
     * @return Picture
     */
    public function loadPicture($image, $worker = NULL) {

        return new Picture($image, $this, $worker);
    }


    /**
     * Ověří zda obrázek v úložišti je aktuální
     * @param $file
     * @param Picture $picture
     * @return mixed
     */
    public function isFileActual($file, Picture $picture) {

        return file_exists($file);
    }


    /**
     * Uloží obrázek do úložiště
     * @param Picture $picture
     * @return mixed
     */
    public function save(Picture $picture) {

        $pictureFile = $this->getFile($picture);

        if(!$this->isFileActual($pictureFile, $picture)) {

            $picture->save($pictureFile);
        }
    }


    /**
     * Vrací cestu k obrázku v úložišti, neověřuje zda obrázek fyzicky existuje.
     * @param Picture $picture
     * @return string absolutní cesta k obrázku
     */
    public function getFile(Picture $picture) {

        return $this->path . '/' . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Vrací absolutní url adresu k obrázku v úložišti
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture) {

        return $this->url . str_replace($this->basePath, '', $this->getFileName($picture));
    }


    /**
     * Vrací název obrázku včetně přípony v úložišti, neověřuje jeho existenci.
     * @param Picture $picture
     * @return string
     */
    protected function getFileName(Picture $picture) {

        $imageFile  = $picture->getImage();
        $imageQuery = $picture->getImageQuery();

        return pathinfo($imageFile, PATHINFO_FILENAME) . '.' . sprintf("%u", crc32($imageQuery)) . '.' . pathinfo($imageFile, PATHINFO_EXTENSION);
    }


    /**
     * Sjednotí formát url adresy
     * @param $url
     * @return mixed
     */
    protected function unifyUrl($url) {

        return preg_replace('/([^:])(\/{2,})/', '$1/', $url);
    }


    /**
     * Sjednotí formát filesystémové cesty
     * @param $path
     * @param string $slash
     * @return mixed
     */
    protected function unifyPath($path, $slash = DIRECTORY_SEPARATOR) {

        return preg_replace('/\\'. $slash .'+/', $slash, str_replace(array('/', "\\"), $slash, $path));
    }

}