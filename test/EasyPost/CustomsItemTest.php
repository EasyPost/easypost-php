<?php

namespace EasyPost\Test;

use EasyPost\CustomsItem;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use VCR\VCR;

class CustomsItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a CustomsItem.
     */
    public function testCreate()
    {
        VCR::insertCassette('customs_items/create.yml');

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
        VCR::insertCassette('customs_items/retrieve.yml');

        $customsItem = CustomsItem::create(Fixture::basicCustomsItem());

        $retrievedCustomsItem = CustomsItem::retrieve($customsItem->id);

        $this->assertInstanceOf('\EasyPost\CustomsItem', $retrievedCustomsItem);
        $this->assertEquals($customsItem, $retrievedCustomsItem);
    }
}
