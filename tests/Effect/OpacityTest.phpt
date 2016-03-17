<?php

require_once 'EffectTestCase.php';

/**
 * Class OpacityTest
 */
class OpacityTest extends EffectTestCase {

    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testOpacityValue($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Opacity;0.4');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new OpacityTest();
$testCase->run();