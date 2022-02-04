<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Refund;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class RefundTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a refund.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('refunds/create.yml');

        $shipment_data = Fixture::one_call_buy_shipment();

        $shipment = Shipment::create($shipment_data);
        $retrieved_shipment = Shipment::retrieve($shipment); // We need to retrieve the shipment so that the tracking_code has time to populate

        $refund = Refund::create([
            'carrier' => 'USPS',
            'tracking_codes' => $retrieved_shipment->tracking_code,
        ]);

        $this->assertStringMatchesFormat('rfnd_%s', $refund[0]->id);
        $this->assertEquals($refund[0]->status, 'submitted');
    }

    /**
     * Test retrieving all refunds.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('refunds/all.yml');

        $refunds = Refund::all([
            'page_size' => Fixture::page_size(),
        ]);

        $refunds_array = $refunds['refunds'];

        $this->assertLessThanOrEqual($refunds_array, Fixture::page_size());
        foreach ($refunds_array as $address) {
            $this->assertInstanceOf('\EasyPost\Refund', $address);
        }

        // Return so other tests can reuse these objects
        return $refunds;
    }

    /**
     * Test retrieving a refund.
     *
     * @param object $refunds
     * @return void
     * @depends testAll
     */
    public function testRetrieve(object $refunds)
    {
        VCR::insertCassette('refunds/retrieve.yml');

        $event = Refund::retrieve($refunds['refunds'][0]);

        $this->assertInstanceOf('\EasyPost\Refund', $event);
        $this->assertStringMatchesFormat('rfnd_%s', $event->id);
    }
}
