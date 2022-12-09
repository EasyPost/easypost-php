<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Util\InternalUtil;

class EasyPostObjectTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Test using `isset` magic method.
     */
    public function testIssetMagicMethod()
    {
        $object = InternalUtil::convertToEasyPostObject(self::$client, Fixture::caAddress1());

        $isset = isset($object->name);

        $this->assertTrue($isset);
    }

    /**
     * Test using `get` magic method with an invalid property.
     */
    public function testGetMagicMethodInvalidProperty()
    {
        $object = InternalUtil::convertToEasyPostObject(self::$client, Fixture::caAddress1());

        $invalidProperty = $object->invalidProperty;

        // TODO: This doesn't capture the `error_log()` call correctly, refactor the code so we can test this output
        $this->assertNull($invalidProperty);
    }

    /**
     * Tests that we can print an object correctly.
     */
    public function testPrintObject()
    {
        $object = InternalUtil::convertToEasyPostObject(self::$client, Fixture::caAddress1());

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
