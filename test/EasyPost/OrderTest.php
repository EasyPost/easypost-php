<?php

namespace EasyPost\Test;

use EasyPost\Error;
use EasyPost\Order;

class OrderTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an Order.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('orders/create.yml');

        $order = Order::create(Fixture::basicOrder());

        $this->assertInstanceOf('\EasyPost\Order', $order);
        $this->assertStringMatchesFormat('order_%s', $order->id);
        $this->assertNotNull($order->rates);
    }

    /**
     * Test retrieving an Order.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('orders/retrieve.yml');

        $order = Order::create(Fixture::basicOrder());

        $retrievedOrder = Order::retrieve($order->id);

        $this->assertInstanceOf('\EasyPost\Order', $retrievedOrder);
        $this->assertEquals($order->id, $retrievedOrder->id);
    }

    /**
     * Test retrieving rates for a order.
     */
    public function testGetRates()
    {
        TestUtil::setupCassette('orders/getRates.yml');

        $order = Order::create(Fixture::basicOrder());

        $rates = $order->getRates();

        $ratesArray = $rates['rates'];

        $this->assertIsArray($ratesArray);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Rate', $ratesArray);
    }

    /**
     * Test buying an Order.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('orders/buy.yml');

        $order = Order::create(Fixture::basicOrder());

        $order->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::uspsService(),
        ]);

        $shipmentsArray = $order['shipments'];

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

        $order = Order::create(Fixture::basicOrder());

        $order->buy($order->rates[0]);

        $shipmentsArray = $order['shipments'];

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

        $order = Order::create(Fixture::basicOrder());

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
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
