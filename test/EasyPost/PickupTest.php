<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Pickup;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

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
    }

    /**
     * Test retrieving a pickup.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('pickups/retrieve.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $pickup_data = Fixture::basic_pickup();
        $pickup_data['shipment'] = $shipment;

        $pickup = Pickup::create($pickup_data);
        $retrieved_pickup = Pickup::retrieve($pickup->id);

        $this->assertInstanceOf('\EasyPost\Pickup', $retrieved_pickup);
        $this->assertEquals($pickup, $retrieved_pickup);
    }

    /**
     * Test buying a pickup.
     *
     * @return Pickup
     */
    public function testBuy()
    {
        VCR::insertCassette('pickups/buy.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $pickup_data = Fixture::basic_pickup();
        $pickup_data['shipment'] = $shipment;

        $pickup = Pickup::create($pickup_data);

        $bought_pickup = $pickup->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::pickup_service(),
        ]);

        $this->assertInstanceOf('\EasyPost\Pickup', $bought_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $bought_pickup->id);
        $this->assertNotNull($bought_pickup->confirmation);
        $this->assertEquals('scheduled', $bought_pickup->status);
    }

    /**
     * Test cancelling a pickup.
     *
     * @return void
     */
    public function testCancel()
    {
        VCR::insertCassette('pickups/cancel.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $pickup_data = Fixture::basic_pickup();
        $pickup_data['shipment'] = $shipment;

        $pickup = Pickup::create($pickup_data);

        $bought_pickup = $pickup->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::pickup_service(),
        ]);

        $cancelled_pickup = $bought_pickup->cancel();

        $this->assertInstanceOf('\EasyPost\Pickup', $cancelled_pickup);
        $this->assertStringMatchesFormat('pickup_%s', $cancelled_pickup->id);
        $this->assertEquals('canceled', $cancelled_pickup->status);
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     *
     * @return void
     */
    public function testLowestRate()
    {
        VCR::insertCassette('pickups/lowestRate.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $pickup_data = Fixture::basic_pickup();
        $pickup_data['shipment'] = $shipment;

        $pickup = Pickup::create($pickup_data);

        // Test lowest rate with no filters
        $lowest_rate = $pickup->lowest_rate();
        $this->assertEquals('NextDay', $lowest_rate['service']);
        $this->assertEquals('0.00', $lowest_rate['rate']);
        $this->assertEquals('USPS', $lowest_rate['carrier']);

        // Test lowest rate with service filter (should error due to bad service)
        try {
            $lowest_rate = $pickup->lowest_rate([], ['BAD SERVICE']);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowest_rate = $pickup->lowest_rate(['BAD CARRIER'], []);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
