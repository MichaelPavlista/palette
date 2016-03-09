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
use Palette\Generator\IServerGenerator;

/**
 * Class Service
 * Simple Palette service implementation.
 * @package Palette
 */
class Service {

    /**
     * @var IPictureGenerator
     */
    protected $generator;


    /**
     * Palette service constructor.
     * @param IPictureGenerator $generator
     */
    public function __construct(IPictureGenerator $generator)  {

        $this->generator = $generator;
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @return null|string
     */
    public function __invoke($image) {

        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get absolute url to image with specified image query string
     * @param $image
     * @param null $imageQuery
     * @return null|string
     */
    public function getUrl($image, $imageQuery = NULL) {

        if(!is_null($imageQuery)) {

            $image .= '@' . $imageQuery;
        }

        return $this->generator->loadPicture($image)->getUrl();
    }


    /**
     * Get Palette picture instance
     * @param $image
     * @return Picture
     */
    public function getPicture($image) {

        return $this->generator->loadPicture($image);
    }


    /**
     * Get Palette generator instance
     * @return IPictureGenerator
     */
    public function getGenerator() {

        return $this->generator;
    }


    /**
     * If generator implements interface IServerGenerator execute server generator backend.
     * @return void
     */
    public function serverResponse() {

        if($this->generator instanceof IServerGenerator) {

            $this->generator->serverResponse();
        }
    }

}