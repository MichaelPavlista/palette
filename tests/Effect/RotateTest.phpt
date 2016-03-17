<?php

require_once 'EffectTestCase.php';

/**
 * Class RotateTest
 */
class RotateTest extends EffectTestCase {

    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testRotatePositive($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Rotate;90');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testRotateNegative($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Rotate;-90');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }


    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testRotateColor($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Rotate;30;#2b45e1');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new RotateTest();
$testCase->run();