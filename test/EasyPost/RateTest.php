<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Rate;

class RateTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test retrieving a rate.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('rates/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::basicShipment());

        $rate = self::$client->rate->retrieve($shipment->rates[0]['id']);

        $this->assertInstanceOf(Rate::class, $rate);
        $this->assertStringMatchesFormat('rate_%s', $rate->id);
    }
}
