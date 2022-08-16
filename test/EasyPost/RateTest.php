<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Rate;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

class RateTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving a rate.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('rates/retrieve.yml');

        $shipment = Shipment::create(Fixture::basicShipment());

        $rate = Rate::retrieve($shipment->rates[0]['id']);

        $this->assertInstanceOf('\EasyPost\Rate', $rate);
        $this->assertStringMatchesFormat('rate_%s', $rate->id);
    }
}
