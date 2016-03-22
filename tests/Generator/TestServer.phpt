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
 * Class TestServer
 */
class TestServer extends Tester\TestCase {

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
        $generator = new Generator\Server(TEMP_DIR, 'http://www.palette.cz/', TEMP_DIR);

        Assert::type('Palette\Generator\IPictureGenerator', $generator);
        Assert::type('Palette\Generator\IServerGenerator', $generator);

        $picture = $generator->loadPicture($testFile . '@Grayscale');

        Assert::type('Palette\Picture', $picture);
        Assert::same($picture->getUrl(), $generator->getUrl($picture));

        $filename = $generator->getFileName($picture);

        Assert::same('opaque.1336346146.jpg', $filename);

        $generatedFilePath = TEMP_DIR . DIRECTORY_SEPARATOR . $filename;

        Assert::same($generatedFilePath, $generator->getPath($picture));
    }

}

$testCase = new TestServer();
$testCase->run();