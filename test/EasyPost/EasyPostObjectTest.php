<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Util;

class EasyPostObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test using `isset` magic method.
     */
    public function testIssetMagicMethod()
    {
        $object = Util::convertToEasyPostObject(Fixture::caAddress1(), null);

        $isset = isset($object->name);

        $this->assertTrue($isset);
    }

    /**
     * Test using `get` magic method.
     */
    public function testGetMagicMethod()
    {
        $object = Util::convertToEasyPostObject(Fixture::caAddress1(), null);

        $invalidProperty = $object->invalidProperty;

        $this->assertNull($invalidProperty);
    }

    /**
     * Tests that we can print an object correctly.
     */
    public function testPrintObject()
    {
        $object = Util::convertToEasyPostObject(Fixture::caAddress1(), null);

        echo $object;
        $this->expectOutputString('{
    "name": "Jack Sparrow",
    "street1": "388 Townsend St",
    "street2": "Apt 20",
    "city": "San Francisco",
    "state": "CA",
    "zip": "94107",
    "country": "US",
    "email": "test@example.com",
    "phone": "5555555555"
}');
    }
}
