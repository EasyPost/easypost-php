<?php

namespace EasyPost\Test;

use EasyPost\Error;
use EasyPost\Pickup;
use EasyPost\Shipment;

class PickupTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a pickup.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('pickups/create.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = Pickup::create($pickupData);

        $this->assertInstanceOf('\EasyPost\Pickup', $pickup);
        $this->assertStringMatchesFormat('pickup_%s', $pickup->id);
        $this->assertNotNull($pickup->pickup_rates);
    }

    /**
     * Test retrieving a pickup.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('pickups/retrieve.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = Pickup::create($pickupData);
        $retrievedPickup = Pickup::retrieve($pickup->id);

        $this->assertInstanceOf('\EasyPost\Pickup', $retrievedPickup);
        $this->assertEquals($pickup, $retrievedPickup);
    }

    /**
     * Test buying a pickup.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('pickups/buy.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = Pickup::create($pickupData);

        $boughtPickup = $pickup->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::pickupService(),
        ]);

        $this->assertInstanceOf('\EasyPost\Pickup', $boughtPickup);
        $this->assertStringMatchesFormat('pickup_%s', $boughtPickup->id);
        $this->assertNotNull($boughtPickup->confirmation);
        $this->assertEquals('scheduled', $boughtPickup->status);
    }

    /**
     * Test cancelling a pickup.
     */
    public function testCancel()
    {
        TestUtil::setupCassette('pickups/cancel.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = Pickup::create($pickupData);

        $boughtPickup = $pickup->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::pickupService(),
        ]);

        $cancelledPickup = $boughtPickup->cancel();

        $this->assertInstanceOf('\EasyPost\Pickup', $cancelledPickup);
        $this->assertStringMatchesFormat('pickup_%s', $cancelledPickup->id);
        $this->assertEquals('canceled', $cancelledPickup->status);
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     */
    public function testLowestRate()
    {
        TestUtil::setupCassette('pickups/lowestRate.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = Pickup::create($pickupData);

        // Test lowest rate with no filters
        $lowestRate = $pickup->lowestRate();
        $this->assertEquals('NextDay', $lowestRate['service']);
        $this->assertEquals('0.00', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with service filter (should error due to bad service)
        try {
            $lowestRate = $pickup->lowestRate([], ['BAD SERVICE']);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $pickup->lowestRate(['BAD CARRIER'], []);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
