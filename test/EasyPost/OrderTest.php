<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Order;
use EasyPost\Test\Fixture;
use VCR\VCR;

class OrderTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an Order.
     */
    public function testCreate()
    {
        VCR::insertCassette('orders/create.yml');

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
        VCR::insertCassette('orders/retrieve.yml');

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
        VCR::insertCassette('orders/getRates.yml');

        $order = Order::create(Fixture::basicOrder());

        $rates = $order->get_rates();

        $ratesArray = $rates['rates'];

        $this->assertIsArray($ratesArray);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Rate', $ratesArray);
    }

    /**
     * Test buying an Order.
     */
    public function testBuy()
    {
        VCR::insertCassette('orders/buy.yml');

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
     * Test various usage alterations of the lowest_rate method.
     */
    public function testLowestRate()
    {
        VCR::insertCassette('orders/lowestRate.yml');

        $order = Order::create(Fixture::basicOrder());

        // Test lowest rate with no filters
        $lowestRate = $order->lowest_rate();
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.57', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with service filter (this rate is higher than the lowest but should filter)
        $lowestRate = $order->lowest_rate([], ['Priority']);
        $this->assertEquals('Priority', $lowestRate['service']);
        $this->assertEquals('7.90', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $order->lowest_rate(['BAD CARRIER'], []);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
