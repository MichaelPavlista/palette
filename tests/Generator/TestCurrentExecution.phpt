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

use Tester\Assert;
use Palette\Generator;

/**
 * Class TestCurrentExecution
 */
class TestCurrentExecution extends Tester\TestCase {

    /**
     * Test palette picture generator
     */
    public function testGenerator() {

        $testFile = TEMP_DIR . DIRECTORY_SEPARATOR . 'opaque.jpg';

        copy('../bin/worker/opaque.jpg', $testFile);

        if(!file_exists($testFile)) {

            throw new Tester\TestCaseException('Test image copy to temp directory failed');
        }

        // PALETTE GENERATOR TEST
        $generator = new Generator\CurrentExecution(TEMP_DIR, 'http://www.palette.cz/', TEMP_DIR);

        Assert::type('Palette\Generator\IPictureGenerator', $generator);

        $picture = $generator->loadPicture($testFile . '@Blur');

        Assert::type('Palette\Picture', $picture);
        Assert::same('http://www.palette.cz/opaque.2782063050.jpg', $picture->getUrl());
        Assert::same('http://www.palette.cz/opaque.2782063050.jpg', $generator->getUrl($picture));

        $filename = $generator->getFileName($picture);

        Assert::same('opaque.2782063050.jpg', $filename);

        $generatedFilePath = TEMP_DIR . DIRECTORY_SEPARATOR . $filename;

        Assert::same($generatedFilePath, $generator->getPath($picture));

        if(!file_exists($generatedFilePath)) {

            throw new Tester\TestCaseException('Generating image variant failed');
        }
    }

}

$testCase = new TestCurrentExecution();
$testCase->run();