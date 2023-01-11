<?php

namespace EasyPost\Test;

use EasyPost\CustomsItem;
use EasyPost\EasyPostClient;

class CustomsItemTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a CustomsItem.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('customs_items/create.yml');

        $customsItem = self::$client->customsItem->create(Fixture::basicCustomsItem());

        $this->assertInstanceOf(CustomsItem::class, $customsItem);
        $this->assertStringMatchesFormat('cstitem_%s', $customsItem->id);
        $this->assertEquals('23.25', $customsItem->value);
    }

    /**
     * Test retrieving a CustomsItem.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('customs_items/retrieve.yml');

        $customsItem = self::$client->customsItem->create(Fixture::basicCustomsItem());

        $retrievedCustomsItem = self::$client->customsItem->retrieve($customsItem->id);

        $this->assertInstanceOf(CustomsItem::class, $retrievedCustomsItem);
        $this->assertEquals($customsItem, $retrievedCustomsItem);
    }
}
