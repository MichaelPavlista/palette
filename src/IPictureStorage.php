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