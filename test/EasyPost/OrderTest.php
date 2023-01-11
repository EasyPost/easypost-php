<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Order;
use EasyPost\Rate;

class OrderTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an Order.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('orders/create.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        $this->assertInstanceOf(Order::class, $order);
        $this->assertStringMatchesFormat('order_%s', $order->id);
        $this->assertNotNull($order->rates);
    }

    /**
     * Test retrieving an Order.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('orders/retrieve.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        $retrievedOrder = self::$client->order->retrieve($order->id);

        $this->assertInstanceOf(Order::class, $retrievedOrder);
        $this->assertEquals($order->id, $retrievedOrder->id);
    }

    /**
     * Test retrieving rates for a order.
     */
    public function testGetRates()
    {
        TestUtil::setupCassette('orders/getRates.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        $rates = self::$client->order->getRates($order->id);

        $ratesArray = $rates['rates'];

        $this->assertIsArray($ratesArray);
        $this->assertContainsOnlyInstancesOf(Rate::class, $ratesArray);
    }

    /**
     * Test buying an Order.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('orders/buy.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        $boughtOrder = self::$client->order->buy(
            $order->id,
            [
                'carrier' => Fixture::usps(),
                'service' => Fixture::uspsService(),
            ]
        );

        $shipmentsArray = $boughtOrder['shipments'];

        foreach ($shipmentsArray as $shipment) {
            $this->assertNotNull($shipment->postage_label);
        }
    }

    /**
     * Test buying an Order with a Rate object.
     */
    public function testBuyRateObject()
    {
        TestUtil::setupCassette('orders/buyRateObject.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        $boughtOrder = self::$client->order->buy($order->id, $order->rates[0]);

        $shipmentsArray = $boughtOrder['shipments'];

        foreach ($shipmentsArray as $shipment) {
            $this->assertNotNull($shipment->postage_label);
        }
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     */
    public function testLowestRate()
    {
        TestUtil::setupCassette('orders/lowestRate.yml');

        $order = self::$client->order->create(Fixture::basicOrder());

        // Test lowest rate with no filters
        $lowestRate = $order->lowestRate();
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.82', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with service filter (this rate is higher than the lowest but should filter)
        $lowestRate = $order->lowestRate([], ['Priority']);
        $this->assertEquals('Priority', $lowestRate['service']);
        $this->assertEquals('8.15', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $order->lowestRate(['BAD CARRIER'], []);
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
