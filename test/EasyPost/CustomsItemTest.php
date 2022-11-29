<?php

namespace EasyPost\Test;

use EasyPost\CustomsItem;

class CustomsItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests('EASYPOST_TEST_API_KEY');
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

        $customsItem = CustomsItem::create(Fixture::basicCustomsItem());

        $this->assertInstanceOf('\EasyPost\CustomsItem', $customsItem);
        $this->assertStringMatchesFormat('cstitem_%s', $customsItem->id);
        $this->assertEquals('23.25', $customsItem->value);
    }

    /**
     * Test retrieving a CustomsItem.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('customs_items/retrieve.yml');

        $customsItem = CustomsItem::create(Fixture::basicCustomsItem());

        $retrievedCustomsItem = CustomsItem::retrieve($customsItem->id);

        $this->assertInstanceOf('\EasyPost\CustomsItem', $retrievedCustomsItem);
        $this->assertEquals($customsItem, $retrievedCustomsItem);
    }
}
