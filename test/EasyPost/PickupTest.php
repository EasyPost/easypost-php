<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Exception\General\EndOfPaginationException;
use Exception;
use EasyPost\Pickup;

class PickupTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a pickup.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('pickups/create.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = self::$client->pickup->create($pickupData);

        $this->assertInstanceOf(Pickup::class, $pickup);
        $this->assertStringMatchesFormat('pickup_%s', $pickup->id);
        $this->assertNotNull($pickup->pickup_rates);
    }

    /**
     * Test retrieving a pickup.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('pickups/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = self::$client->pickup->create($pickupData);
        $retrievedPickup = self::$client->pickup->retrieve($pickup->id);

        $this->assertInstanceOf(Pickup::class, $retrievedPickup);
        $this->assertEquals($pickup, $retrievedPickup);
    }

    /**
     * Test retrieving all shipments.
     */
    public function testAll()
    {
        TestUtil::setupCassette('pickups/all.yml');

        $pickups = self::$client->pickup->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $pickupsArray = $pickups['pickups'];

        $this->assertLessThanOrEqual($pickupsArray, Fixture::pageSize());
        $this->assertNotNull($pickups['has_more']);
        $this->assertContainsOnlyInstancesOf(Pickup::class, $pickupsArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('pickups/getNextPage.yml');

        try {
            $pickups = self::$client->pickup->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->pickup->getNextPage($pickups, Fixture::pageSize());

            $firstIdOfFirstPage = $pickups['pickups'][0]->id;
            $secondIdOfSecondPage = $nextPage['pickups'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
    }

    /**
     * Test buying a pickup.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('pickups/buy.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = self::$client->pickup->create($pickupData);

        $boughtPickup = self::$client->pickup->buy(
            $pickup->id,
            [
                'carrier' => Fixture::usps(),
                'service' => Fixture::pickupService(),
            ]
        );

        $this->assertInstanceOf(Pickup::class, $boughtPickup);
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

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = self::$client->pickup->create($pickupData);

        $boughtPickup = self::$client->pickup->buy(
            $pickup->id,
            [
                'carrier' => Fixture::usps(),
                'service' => Fixture::pickupService(),
            ]
        );

        $cancelledPickup = self::$client->pickup->cancel($boughtPickup->id);

        $this->assertInstanceOf(Pickup::class, $cancelledPickup);
        $this->assertStringMatchesFormat('pickup_%s', $cancelledPickup->id);
        $this->assertEquals('canceled', $cancelledPickup->status);
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     */
    public function testLowestRate()
    {
        TestUtil::setupCassette('pickups/lowestRate.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $pickupData = Fixture::basicPickup();
        $pickupData['shipment'] = $shipment;

        $pickup = self::$client->pickup->create($pickupData);

        // Test lowest rate with no filters
        $lowestRate = $pickup->lowestRate();
        $this->assertEquals('NextDay', $lowestRate['service']);
        $this->assertEquals('0.00', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with service filter (should error due to bad service)
        try {
            $lowestRate = $pickup->lowestRate([], ['BAD SERVICE']);
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $pickup->lowestRate(['BAD CARRIER'], []);
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
