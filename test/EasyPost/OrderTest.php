<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Order;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

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
     * @return Order
     */
    public function testCreate()
    {
        VCR::insertCassette('orders/create.yml');

        $order = Order::create([
            'to_address' => Fixture::basic_address(),
            'from_address' => Fixture::basic_address(),
            'shipments' => [Fixture::basic_shipment()],
        ]);

        $this->assertInstanceOf('\EasyPost\Order', $order);
        $this->assertStringMatchesFormat('order_%s', $order->id);
        $this->assertNotNull($order->rates);

        // Return so other tests can reuse this object
        return $order;
    }

    /**
     * Test retrieving an Order.
     *
     * @param Order $order
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Order $order)
    {
        VCR::insertCassette('orders/retrieve.yml');

        $retrieved_order = Order::retrieve($order->id);

        $this->assertInstanceOf('\EasyPost\Order', $retrieved_order);
        $this->assertEquals($order->id, $retrieved_order->id);
    }

    /**
     * Test retrieving rates for a order.
     *
     * @param Order $order
     * @return void
     * @depends testCreate
     */
    public function testGetRates(Order $order)
    {
        VCR::insertCassette('orders/getRates.yml');

        $rates = $order->get_rates();

        $rates_array = $rates['rates'];

        $this->assertIsArray($rates_array);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Rate', $rates_array);
    }

    /**
     * Test buying an Order.
     *
     * @param Order $order
     * @return void
     * @depends testCreate
     */
    public function testBuy(Order $order)
    {
        VCR::insertCassette('orders/buy.yml');

        $order->buy([
            'carrier' => Fixture::usps(),
            'service' => Fixture::usps_service(),
        ]);

        $shipments_array = $order['shipments'];

        foreach ($shipments_array as $shipment) {
            $this->assertNotNull($shipment->postage_label);
        }
    }
}
