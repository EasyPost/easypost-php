<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Rate;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class RateTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving a rate.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('rates/retrieve.yml');

        $shipment = Shipment::create(Fixture::basic_shipment());

        $rate = Rate::retrieve($shipment->rates[0]['id']);

        $this->assertInstanceOf('\EasyPost\Rate', $rate);
        $this->assertStringMatchesFormat('rate_%s', $rate->id);
    }
}
