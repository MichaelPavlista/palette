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
use Palette\Utils;

/**
 * Class TestCompare
 */
class TestCompare extends Tester\TestCase {

    /**
     * Test if images is equal
     */
    public function testCompareSame() {

        $compare = new Utils\Compare('../bin/compare/same1.png', '../bin/compare/same2.png');

        Assert::true($compare->isDimensionsEqual());
        Assert::true($compare->isEqual());
    }


    /**
     * Test when image dimensions is equal but images not
     */
    public function testCompareDifferent() {

        $compare = new Utils\Compare('../bin/compare/same1.png', '../bin/compare/different.whole.png');

        Assert::false($compare->isDimensionsEqual());
        Assert::false($compare->isEqual());
    }


    /**
     * Test when images is complete different
     */
    public function testCompareDifferentSize() {

        $compare = new Utils\Compare('../bin/compare/same1.png', '../bin/compare/different.size.png');

        Assert::true($compare->isDimensionsEqual());
        Assert::false($compare->isEqual());
    }

}

$testCase = new TestCompare();
$testCase->run();