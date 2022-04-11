<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Pickup;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class PickupTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a pickup.
     *
     * @return Pickup
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

        // Return so other tests can reuse this object
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
        $this->assertEquals($pickup, $retrieved_pickup);
    }

    /**
     * Test buying a pickup.
     *
     * @param Pickup $pickup
     * @return Pickup
     * @depends testCreate
     */
    public function testBuy(Pickup $pickup)
    {
        VCR::insertCassette('pickups/buy.yml');

        $bought_pickup = $pickup->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::pickup_service(),
        ]);

        $this->assertInstanceOf('\EasyPost\Pickup', $bought_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $bought_pickup->id);
        $this->assertNotNull($bought_pickup->confirmation);
        $this->assertEquals('scheduled', $bought_pickup->status);

        // Return so other tests can reuse this object
        return $pickup;
    }

    /**
     * Test cancelling a pickup.
     *
     * @param Pickup $pickup
     * @return void
     * @depends testBuy
     */
    public function testCancel(Pickup $pickup)
    {
        VCR::insertCassette('pickups/cancel.yml');

        $cancelled_pickup = $pickup->cancel();

        $this->assertInstanceOf('\EasyPost\Pickup', $cancelled_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $cancelled_pickup->id);
        $this->assertEquals('canceled', $cancelled_pickup->status);
    }
}
