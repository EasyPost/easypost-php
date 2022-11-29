<?php

namespace EasyPost\Test;

use EasyPost\CustomsInfo;

class CustomsInfoTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a CustomsInfo.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('customs_info/create.yml');

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
        TestUtil::setupCassette('customs_info/retrieve.yml');

        $customsInfo = CustomsInfo::create(Fixture::basicCustomsInfo());

        $retrievedCustomsInfo = CustomsInfo::retrieve($customsInfo->id);

        $this->assertInstanceOf('\EasyPost\CustomsInfo', $retrievedCustomsInfo);
        $this->assertEquals($customsInfo, $retrievedCustomsInfo);
    }
}
