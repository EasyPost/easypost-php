<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\CustomsInfo;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class CustomsInfoTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a CustomsInfo.
     *
     * @return CustomsInfo
     */
    public function testCreate()
    {
        VCR::insertCassette('customs_info/create.yml');

        $customs_item = CustomsInfo::create(Fixture::basic_customs_info());

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $customs_item);
        $this->assertStringMatchesFormat('cstinfo_%s', $customs_item->id);
        $this->assertEquals($customs_item->eel_pfc, 'NOEEI 30.37(a)');

        // Return so other tests can reuse this object
        return $customs_item;
    }

    /**
     * Test retrieving a CustomsInfo.
     *
     * @param CustomsInfo $customs_item
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(CustomsInfo $customs_item)
    {
        VCR::insertCassette('customs_info/retrieve.yml');

        $retrieved_customs_item = CustomsInfo::retrieve($customs_item->id);

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $retrieved_customs_item);
        $this->assertEquals($retrieved_customs_item, $customs_item);
    }
}
