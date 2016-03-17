<?php

require_once 'EffectTestCase.php';

/**
 * Class SephiaTest
 */
class SephiaTest extends EffectTestCase {

    /**
     * @param $imagePath
     * @param $worker
     * @param $extension
     * @dataProvider getEffectArgs
     */
    public function testSephia($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker, 'Sephia');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new SephiaTest();
$testCase->run();