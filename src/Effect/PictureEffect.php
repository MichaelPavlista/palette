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

namespace Palette\Effect;

use Palette\Picture;
use ReflectionMethod;

/**
 * Class PictureEffect
 */
abstract class PictureEffect {

    /**
     * @var array effect settings
     */
    protected $settings = array();


    /**
     * Apply effect on picture
     * @param Picture $picture
     */
    abstract function apply(Picture $picture);


    /**
     * PictureEffect constructor.
     */
    public function __construct() {

    }


    /**
     * Restore effect state from array
     * @param array $settings
     */
    public function restore(array $settings = array()) {

        $settings = array_values($settings);

        $reflection = new ReflectionMethod(get_class($this), '__construct');
        $alreadySet = $reflection->getNumberOfParameters();

        $index = 0;

        foreach($this->settings as &$setting) {

            if($alreadySet > $index) {

                continue;
            }

            if(array_key_exists($index, $settings)) {

                $setting = $settings[$index] === '' ? NULL : $settings[$index];
            }

            $index++;
        }
    }


    /**
     * Calculates new picture dimension after applying this effect
     * @param int $width
     * @param int $height
     * @return array w,h
     */
    public function getNewDimensions($width, $height) {

        return array(

            'w' => $width,
            'h' => $height,
        );
    }


    /**
     * Get effect setting value
     * @param string $name
     * @return mixed
     */
    final public function __get($name) {

        if($this->__isset($name)) {

            return $this->settings[$name];
        }

        return NULL;
    }


    /**
     * Sets effect setting value
     * @param string $name
     * @param mixed $value
     */
    final public function __set($name, $value) {

        if($this->__isset($name)) {

            $this->settings[$name] = $value;
        }
        else {

            trigger_error('Unknown effect parameter: ' . $name, E_USER_WARNING);
        }
    }


    /**
     * Is effect setting already defined?
     * @param $name
     * @return bool
     */
    final public function __isset($name) {

        return array_key_exists($name, $this->settings);
    }


    /**
     * Delete effect setting value
     * @param $name
     */
    public function __unset($name) {

        $this->settings[$name] = NULL;
    }


    /**
     * Hexadecimal color to RGB conversion
     * @param string $hex color
     * @param bool $returnString
     * @return array|string
     */
    public function hex2rgb($hex, $returnString = FALSE) {

        $hex = str_replace('#', '', $hex);

        if(strlen($hex) == 3) {

            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        }
        else {

            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }

        if($returnString) {

            return implode(', ', array($r, $g, $b));
        }

        return array($r, $g, $b);
    }


    /**
     * Renders Palette image query for current effect
     * @return string
     */
    public function __toString() {

        $arguments = ';' . implode(';', $this->settings);

        return substr(get_class($this), strrpos(get_class($this), '\\') + 1) . substr($arguments, 0, strlen($arguments));
    }

}