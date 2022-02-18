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

        $customs_info = CustomsInfo::create(Fixture::basic_customs_info());

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $customs_info);
        $this->assertStringMatchesFormat('cstinfo_%s', $customs_info->id);
        $this->assertEquals('NOEEI 30.37(a)', $customs_info->eel_pfc);

        // Return so other tests can reuse this object
        return $customs_info;
    }

    /**
     * Test retrieving a CustomsInfo.
     *
     * @param CustomsInfo $customs_info
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(CustomsInfo $customs_info)
    {
        VCR::insertCassette('customs_info/retrieve.yml');

        $retrieved_customs_info = CustomsInfo::retrieve($customs_info->id);

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $retrieved_customs_info);
        $this->assertEquals($customs_info, $retrieved_customs_info);
    }
}
