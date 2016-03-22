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

require_once '../bootstrap.php';

use Palette\Picture;

/**
 * Class EffectTestCase
 */
class EffectTestCase extends Tester\TestCase {

    /**
     * Data provider for palette effect tests
     * @return array
     */
    public function getEffectArgs() {

        return $this->cartesian(array(

            array('../bin/worker/opaque.jpg', '../bin/worker/transparent.png', '../bin/worker/logo.gif'),
            array(Picture::WORKER_GD, Picture::WORKER_IMAGICK),
            array('jpg', 'png', 'gif')
        ));
    }


    /**
     * Get palette image instance for effect testing
     * @param $path
     * @param $worker
     * @return Palette\Picture
     */
    protected function getPicture($path, $worker, $query = '') {

        $sourceImage = realpath($path);

        Tester\Assert::truthy($sourceImage, 'Image for effect test is missing');

        if($query) {

            $sourceImage .= '@' . $query;
        }

        if($worker === Picture::WORKER_GD) {

            $picture = new NoImagickPicture($sourceImage, NULL, $worker);
        }
        else {

            $picture = new Palette\Picture($sourceImage, NULL, $worker);
        }

        return $picture;
    }


    /**
     * Compare two images for specified method and worker
     * @param $testImage
     * @param $sourceImage
     * @param $worker
     * @param $method
     * @throws Tester\TestCaseException
     */
    protected function compare($testImage, $sourceImage, $worker, $method) {

        $compareDir  = realpath('../bin/worker/') . DIRECTORY_SEPARATOR;
        $compareFile = $compareDir . $worker . DIRECTORY_SEPARATOR . str_replace('::', DIRECTORY_SEPARATOR, strtolower($method)) .
            '.' . pathinfo($sourceImage, PATHINFO_FILENAME) .
            '.' . pathinfo($testImage, PATHINFO_EXTENSION);

        if(!file_exists($compareFile)) {

            @mkdir(pathinfo($compareFile, PATHINFO_DIRNAME), 0777, TRUE);

            rename($testImage, $compareFile);

            if(!file_exists($compareFile)) {

                throw new Tester\TestCaseException('Generating effect test image failed');
            }
        }
        else {

            $compare = new Palette\Utils\Compare($testImage, $compareFile);

            if(!$compare->isEqual()) {

                throw new Tester\TestCaseException('Image is not the same');
            }
        }
    }


    /**
     * Get new temp image path
     * @param $extension
     * @return string
     */
    protected function tempFile($extension) {

        return TEMP_DIR . DIRECTORY_SEPARATOR . sha1(uniqid('', TRUE)) . '.' . $extension;
    }


    /**
     * Generate cartesian values sum
     * @param array $input
     * @return array
     */
    protected function cartesian(array $input) {

        $input  = array_filter($input);
        $result = array(array());

        foreach($input as $key => $values) {

            $append = [];

            foreach($result as $product) {

                foreach($values as $item) {

                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

}