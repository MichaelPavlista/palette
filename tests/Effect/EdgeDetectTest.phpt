<?php

require_once 'EffectTestCase.php';

/**
 * Class EdgeDetectTest
 */
class EdgeDetectTest extends EffectTestCase {

    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testEdgeDetect($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'EdgeDetect');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new EdgeDetectTest();
$testCase->run();