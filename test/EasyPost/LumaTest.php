<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class LumaTest extends TestCase
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
     * Test that we get service recommendations from Luma based on your ruleset.
     */
    public function testGetPromise(): void
    {
        TestUtil::setupCassette('luma/getPromise.yml');

        $basicShipment = Fixture::basicShipment();
        $basicShipment['ruleset_name'] = Fixture::lumaRulesetName();
        $basicShipment['planned_ship_date'] = Fixture::lumaPlannedShipDate();

        $response = self::$client->luma->getPromise($basicShipment);

        $this->assertNotNull($response->luma_selected_rate);
    }
}
