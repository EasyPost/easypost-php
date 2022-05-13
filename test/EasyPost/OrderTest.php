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
     * Test creating an Order.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('orders/create.yml');

        $order = Order::create(Fixture::basic_order());

        $this->assertInstanceOf('\EasyPost\Order', $order);
        $this->assertStringMatchesFormat('order_%s', $order->id);
        $this->assertNotNull($order->rates);
    }

    /**
     * Test retrieving an Order.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('orders/retrieve.yml');

        $order = Order::create(Fixture::basic_order());

        $retrieved_order = Order::retrieve($order->id);

        $this->assertInstanceOf('\EasyPost\Order', $retrieved_order);
        $this->assertEquals($order->id, $retrieved_order->id);
    }

    /**
     * Test retrieving rates for a order.
     *
     * @return void
     */
    public function testGetRates()
    {
        VCR::insertCassette('orders/getRates.yml');

        $order = Order::create(Fixture::basic_order());

        $rates = $order->get_rates();

        $rates_array = $rates['rates'];

        $this->assertIsArray($rates_array);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Rate', $rates_array);
    }

    /**
     * Test buying an Order.
     *
     * @return void
     */
    public function testBuy()
    {
        VCR::insertCassette('orders/buy.yml');

        $order = Order::create(Fixture::basic_order());

        $order->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::usps_service(),
        ]);

        $shipments_array = $order['shipments'];

        foreach ($shipments_array as $shipment) {
            $this->assertNotNull($shipment->postage_label);
        }
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     *
     * @return void
     */
    public function testLowestRate()
    {
        VCR::insertCassette('orders/lowestRate.yml');

        $order = Order::create(Fixture::basic_order());

        // Test lowest rate with no filters
        $lowest_rate = $order->lowest_rate();
        $this->assertEquals('First', $lowest_rate['service']);
        $this->assertEquals('5.49', $lowest_rate['rate']);
        $this->assertEquals('USPS', $lowest_rate['carrier']);

        // Test lowest rate with service filter (this rate is higher than the lowest but should filter)
        $lowest_rate = $order->lowest_rate([], ['Priority']);
        $this->assertEquals('Priority', $lowest_rate['service']);
        $this->assertEquals('7.37', $lowest_rate['rate']);
        $this->assertEquals('USPS', $lowest_rate['carrier']);

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowest_rate = $order->lowest_rate(['BAD CARRIER'], []);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
