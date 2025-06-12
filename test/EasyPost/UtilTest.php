<?php

namespace EasyPost\Test;

use EasyPost\Util\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * Test converting an EasyPost object to an array.
     */
    public function testConvertEasyPostObjectToArray(): void
    {
        $object = Util::convertEasyPostObjectToArray(Fixture::caAddress1());

        $this->assertEquals('Jack Sparrow', $object['name']);
    }
}
