<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\CustomsItem;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class CustomsItemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a CustomsItem.
     *
     * @return CustomsItem
     */
    public function testCreate()
    {
        VCR::insertCassette('customs_items/create.yml');

        $customs_item = CustomsItem::create(Fixture::basic_customs_item());

        $this->assertInstanceOf('\EasyPost\CustomsItem', $customs_item);
        $this->assertStringMatchesFormat('cstitem_%s', $customs_item->id);
        $this->assertEquals($customs_item->value, '23.0');

        // Return so other tests can reuse this object
        return $customs_item;
    }

    /**
     * Test retrieving a CustomsItem.
     *
     * @param CustomsItem $customs_item
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(CustomsItem $customs_item)
    {
        VCR::insertCassette('customs_items/retrieve.yml');

        $retrieved_customs_item = CustomsItem::retrieve($customs_item->id);

        $this->assertInstanceOf('\EasyPost\CustomsItem', $retrieved_customs_item);
        $this->assertEquals($retrieved_customs_item, $customs_item);
    }
}
