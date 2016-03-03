<?php

namespace Palette;

/**
 * Interface IPictureStorage
 * @package Palette
 */
interface IPictureStorage {

    /**
     * Získání instance obrázku náležícímu do tohoto úložiště
     * @param string $image cesta k obrázku
     * @param null $worker
     * @return Picture
     */
    public function loadPicture($image, $worker = NULL);


    /**
     * Ověří zda obrázek v úložišti je aktuální
     * @param $file
     * @param Picture $picture
     * @return mixed
     */
    public function isFileActual($file, Picture $picture);


    /**
     * Uloží obrázek do úložiště
     * @param Picture $picture
     * @return mixed
     */
    public function save(Picture $picture);


    /**
     * Vrací cestu k obrázku v úložišti, neověřuje zda obrázek fyzicky existuje.
     * @param Picture $picture
     * @return string absolutní cesta k obrázku
     */
    public function getFile(Picture $picture);


    /**
     * Vrací absolutní url adresu k obrázku v úložišti
     * @param Picture $picture
     * @return string
     */
    public function getUrl(Picture $picture);

}