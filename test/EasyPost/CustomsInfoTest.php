<?php

namespace EasyPost\Test;

use EasyPost\CustomsInfo;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use VCR\VCR;

class CustomsInfoTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a CustomsInfo.
     */
    public function testCreate()
    {
        VCR::insertCassette('customs_info/create.yml');

        $customsInfo = CustomsInfo::create(Fixture::basicCustomsInfo());

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $customsInfo);
        $this->assertStringMatchesFormat('cstinfo_%s', $customsInfo->id);
        $this->assertEquals('NOEEI 30.37(a)', $customsInfo->eel_pfc);
    }

    /**
     * Test retrieving a CustomsInfo.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('customs_info/retrieve.yml');

        $customsInfo = CustomsInfo::create(Fixture::basicCustomsInfo());

        $retrievedCustomsInfo = CustomsInfo::retrieve($customsInfo->id);

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $retrievedCustomsInfo);
        $this->assertEquals($customsInfo, $retrievedCustomsInfo);
    }
}
