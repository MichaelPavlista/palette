<?php

require_once 'EffectTestCase.php';

/**
 * Class ResizeTest
 */
class ResizeTest extends EffectTestCase {

    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeFit($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;fit');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeCrop($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;crop');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeFill($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;fill');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeStretch($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;stretch');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeExact($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;exact');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testResizeExactColor($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Resize;100;100;exact;#2b45e1');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new ResizeTest();
$testCase->run();