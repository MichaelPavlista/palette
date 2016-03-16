<?php

require_once '../bootstrap.php';

use Palette\Picture;

class EffectTestCase extends Tester\TestCase {



    public function getEffectArgs() {

        return $this->cartesian([

            ['../images/opaque.jpg', '../images/transparent.png'],
            [Picture::WORKER_GD, Picture::WORKER_IMAGICK],
            ['jpg', 'png']
        ]);
    }







    /**
     * @param $path
     * @param $worker
     * @return Palette\Picture
     */
    protected function getPicture($path, $worker) {

        $sourceImage = realpath($path);

        Tester\Assert::truthy($sourceImage, 'Image for effect test is missing');

        $picture = new Palette\Picture($sourceImage, NULL, $worker);

        return $picture;
    }




    protected function compare($testImage, $sourceImage, $worker, $method) {

        $compareDir  = realpath('../images/') . DIRECTORY_SEPARATOR;
        $compareFile = $compareDir . str_replace('::', DIRECTORY_SEPARATOR, strtolower($method)) .
            '.' . pathinfo($sourceImage, PATHINFO_FILENAME) .
            '.' . $worker . '.' . pathinfo($testImage, PATHINFO_EXTENSION);

        if(!file_exists($compareFile)) {

            @mkdir(pathinfo($compareFile, PATHINFO_DIRNAME), 0777, TRUE);

            rename($testImage, $compareFile);

            //Tester\Environment::skip('Test requires image to compare. Image has been generated.');
        }
        else {

            $compare = new Palette\Utils\Compare($testImage, $compareFile);

            if(!$compare->isEqual()) {

                throw new Tester\TestCaseException('Not the same');
            }
        }
    }




    /**
     * @param $extension
     * @return string
     */
    protected function tempFile($extension) {

        return TEMP_DIR . DIRECTORY_SEPARATOR . sha1(uniqid('', TRUE)) . '.' . $extension;
    }




    /**
     * @param array $input
     * @return array
     */
    protected function cartesian(array $input) {

        $input  = array_filter($input);
        $result = [[]];

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