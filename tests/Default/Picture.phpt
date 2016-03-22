<?php

require_once '../bootstrap.php';

use Tester\Assert;

/**
 * Class PictureTest
 */
class PictureTest extends Tester\TestCase {

    /**
     * Test basic palette picture methods
     */
    public function testParseImageQuery() {

        $image = '../bin/worker/transparent.png';

        $picture = new Palette\Picture($image . '@Grayscale&Blur;2');

        Assert::same(realpath($image),  $picture->getImage());
        Assert::same('Grayscale;&Blur;2&Quality;100', $picture->getImageQuery());
        Assert::same(NULL, $picture->getUrl());
    }


    /**
     * Test resource handling methods
     */
    public function testWorker() {

        $tempImage = $this->tempImage();

        $picture = new Palette\Picture('../bin/worker/transparent.png', NULL, Palette\Picture::WORKER_GD);
        $picture->effect('Grayscale');
        $picture->save($tempImage);

        if(!file_exists($tempImage)) {

            new Tester\TestCaseException('Picture saving failed');
        }

        Assert::true($picture->isGd());
        Assert::same('Grayscale;&Quality;100', $picture->getImageQuery());
        Assert::type('resource', $picture->getResource());
        Assert::type('resource', $picture->getResource(Palette\Picture::WORKER_GD));
        Assert::type('Imagick', $picture->getResource(Palette\Picture::WORKER_IMAGICK));
    }


    /**
     * Set & get picture resource gd tests
     */
    public function testResourceSetGd() {

        $picture = new Palette\Picture('../bin/worker/transparent.png', NULL, Palette\Picture::WORKER_GD);

        $resource = $picture->getResource(Palette\Picture::WORKER_GD);
        $picture->setResource($resource);

        Assert::type('resource', $picture->getResource());

        $resource = $picture->getResource(Palette\Picture::WORKER_IMAGICK);
        $picture->setResource($resource);

        Assert::type('resource', $picture->getResource());
    }


    /**
     * Set & get picture resource imagick tests
     */
    public function testResourceSetImagick() {

        $picture = new Palette\Picture('../bin/worker/transparent.png', NULL, Palette\Picture::WORKER_IMAGICK);

        $resource = $picture->getResource(Palette\Picture::WORKER_GD);
        $picture->setResource($resource);

        Assert::type('Imagick', $picture->getResource());

        $resource = $picture->getResource(Palette\Picture::WORKER_IMAGICK);
        $picture->setResource($resource);

        Assert::type('Imagick', $picture->getResource());
    }


    /**
     * Test default picture effects
     */
    public function testDefaultEffects() {

        $picture = new Palette\Picture('../bin/worker/transparent.png', NULL, Palette\Picture::WORKER_IMAGICK);
        $picture->resize(100, 150, Palette\Effect\Resize::MODE_CROP);
        $picture->quality(70);

        Assert::same('Resize;100;150;crop;0;&Quality;70', $picture->getImageQuery());
    }


    /**
     * Return path to unique temp png file
     * @return string
     */
    protected function tempImage() {

        return TEMP_DIR . DIRECTORY_SEPARATOR . sha1(uniqid('', TRUE)) . '.png';
    }

}

$testCase = new PictureTest();
$testCase->run();