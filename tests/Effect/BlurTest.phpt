<?php

require_once 'EffectTestCase.php';

/**
 * Class TestEffects
 */
class BlurTest extends EffectTestCase {

    /**
     * TestBlur constructor.
     * @param string $imagePath
     * @param string $worker
     * @param string $extension
     * @dataProvider getEffectArgs
     */
    public function testBlur($imagePath, $worker, $extension) {

        $tempFile = $this->tempFile($extension);

        $picture = $this->getPicture($imagePath, $worker);
        $picture->effect('Blur');
        $picture->save($tempFile);

        $this->compare($tempFile, $imagePath, $worker, __METHOD__);
    }

}

$testCase = new BlurTest();
$testCase->run();