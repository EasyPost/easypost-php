<?php

namespace EasyPost\Test;

use EasyPost\Util\Util;

class UtilTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test converting an EasyPost object to an array.
     */
    public function testConvertEasyPostObjectToArray()
    {
        $object = Util::convertEasyPostObjectToArray(Fixture::caAddress1());

        $this->assertEquals('Jack Sparrow', $object['name']);
    }
}
