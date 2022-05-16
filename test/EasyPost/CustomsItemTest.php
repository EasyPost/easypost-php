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
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('customs_items/create.yml');

        $customs_item = CustomsItem::create(Fixture::basic_customs_item());

        $this->assertInstanceOf('\EasyPost\CustomsItem', $customs_item);
        $this->assertStringMatchesFormat('cstitem_%s', $customs_item->id);
        $this->assertEquals('23.0', $customs_item->value);
    }

    /**
     * Test retrieving a CustomsItem.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('customs_items/retrieve.yml');

        $customs_item = CustomsItem::create(Fixture::basic_customs_item());

        $retrieved_customs_item = CustomsItem::retrieve($customs_item->id);

        $this->assertInstanceOf('\EasyPost\CustomsItem', $retrieved_customs_item);
        $this->assertEquals($customs_item, $retrieved_customs_item);
    }
}
