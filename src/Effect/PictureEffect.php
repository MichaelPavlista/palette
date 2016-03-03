<?php

namespace Palette\Effect;

use Palette\Picture;
use ReflectionMethod;

/**
 * Class PictureEffect
 */
abstract class PictureEffect {

    /**
     * @var array nastavení efektu
     */
    protected $settings = array();



    /**
     * Efekt obrázku
     */
    public function __construct() {

    }


    /**
     * Aplikuje efekt na obrázek
     * @param Picture $picture
     */
    abstract function apply(Picture $picture);


    /**
     * Získání hodnoty nastavení efektu
     * @param $name
     * @return mixed
     */
    final public function __get($name) {

        if($this->__isset($name)) {

            return $this->settings[$name];
        }

        return NULL;
    }


    /**
     * Nastavení hodnoty nastavení efektu
     * @param $name
     * @param $value
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
     * Zjištění zda je hodnota nastavení efektu existuje
     * @param $name
     * @return bool
     */
    final public function __isset($name) {

        return array_key_exists($name, $this->settings);
    }


    /**
     * Smaže hodnotu nastavení efektu
     * @param $name
     */
    public function __unset($name) {

        $this->settings[$name] = NULL;
    }


    /**
     * Obnoví nastavení efektu z numerického pole
     * @param $settings
     */
    public function restore($settings) {

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
     * @param $hex
     * @param bool $returnString
     * @return array
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
     * Vygenerování ImageQuery pro aktuální efekt
     * @return string
     */
    public function __toString() {

        $arguments = ';' . implode(';', $this->settings);

        return substr(get_class($this), strrpos(get_class($this), '\\') + 1) . substr($arguments, 0, strlen($arguments));
    }


    /**
     * @param $width
     * @param $height
     * @return array
     */
    public function getNewDimensions($width, $height) {

        return array(

            'w' => $width,
            'h' => $height,
        );
    }

}