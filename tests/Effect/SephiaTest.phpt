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