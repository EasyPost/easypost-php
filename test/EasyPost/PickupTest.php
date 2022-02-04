<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Pickup;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class PickupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a pickup.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('pickups/create.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $pickup_data = Fixture::basic_pickup();
        $pickup_data['shipment'] = $shipment;

        $pickup = Pickup::create($pickup_data);

        $this->assertInstanceOf('\EasyPost\Pickup', $pickup);
        $this->assertStringMatchesFormat('pickup_%s', $pickup->id);
        $this->assertNotNull($pickup->pickup_rates);

        // Return so the `retrieve` test can reuse this object
        return $pickup;
    }

    /**
     * Test retrieving a pickup.
     *
     * @param Pickup $pickup
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Pickup $pickup)
    {
        VCR::insertCassette('pickups/retrieve.yml');

        $retrieved_pickup = Pickup::retrieve($pickup->id);

        $this->assertInstanceOf('\EasyPost\Pickup', $retrieved_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $retrieved_pickup->id);
    }

    /**
     * Test buying a pickup.
     *
     * @param Pickup $pickup
     * @return void
     * @depends testCreate
     */
    public function testBuy(Pickup $pickup)
    {
        VCR::insertCassette('pickups/buy.yml');

        $bought_pickup = $pickup->buy([
            'carrier' => 'USPS',
            'service' => 'NextDay',
        ]);

        $this->assertInstanceOf('\EasyPost\Pickup', $bought_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $bought_pickup->id);
        $this->assertNotNull($bought_pickup->confirmation);
        $this->assertEquals($bought_pickup->status, 'scheduled');
    }

    /**
     * Test buyin a pickup.
     *
     * @param Pickup $pickup
     * @return void
     * @depends testCreate
     */
    public function testCancel(Pickup $pickup)
    {
        VCR::insertCassette('pickups/cancel.yml');

        $cancelled_pickup = $pickup->cancel();

        $this->assertInstanceOf('\EasyPost\Pickup', $cancelled_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $cancelled_pickup->id);
        $this->assertNotNull($cancelled_pickup->status, 'canceled');
    }
}