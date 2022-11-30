<?php

namespace EasyPost\Test;

use EasyPost\Rate;
use EasyPost\Shipment;

class RateTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving a rate.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('rates/retrieve.yml');

        $shipment = Shipment::create(Fixture::basicShipment());

        $rate = Rate::retrieve($shipment->rates[0]['id']);

        $this->assertInstanceOf('\EasyPost\Rate', $rate);
        $this->assertStringMatchesFormat('rate_%s', $rate->id);
    }
}
