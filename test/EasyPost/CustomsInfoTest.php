<?php

namespace EasyPost\Test;

use EasyPost\CustomsInfo;
use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class CustomsInfoTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_TEST_API_KEY'));
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
    public function testCreate(): void
    {
        TestUtil::setupCassette('customs_info/create.yml');

        $customsInfo = self::$client->customsInfo->create(Fixture::basicCustomsInfo());

        $this->assertInstanceOf(CustomsInfo::class, $customsInfo);
        $this->assertStringMatchesFormat('cstinfo_%s', $customsInfo->id);
        $this->assertEquals('NOEEI 30.37(a)', $customsInfo->eel_pfc);
    }

    /**
     * Test retrieving a CustomsInfo.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('customs_info/retrieve.yml');

        $customsInfo = self::$client->customsInfo->create(Fixture::basicCustomsInfo());

        $retrievedCustomsInfo = self::$client->customsInfo->retrieve($customsInfo->id);

        $this->assertInstanceOf(CustomsInfo::class, $retrievedCustomsInfo);
        $this->assertEquals($customsInfo, $retrievedCustomsInfo);
    }
}
