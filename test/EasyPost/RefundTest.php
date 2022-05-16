<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Refund;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

class RefundTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a refund.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('refunds/create.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());
        $retrieved_shipment = Shipment::retrieve($shipment); // We need to retrieve the shipment so that the tracking_code has time to populate

        $refund = Refund::create([
            'carrier' => Fixture::usps(),
            'tracking_codes' => $retrieved_shipment->tracking_code,
        ]);

        $this->assertStringMatchesFormat('rfnd_%s', $refund[0]->id);
        $this->assertEquals('submitted', $refund[0]->status);
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
        $this->assertNotNull($refunds['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Refund', $refunds_array);

        // Return so other tests can reuse these objects
        return $refunds;
    }

    /**
     * Test retrieving a refund.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('refunds/retrieve.yml');

        $refunds = Refund::all([
            'page_size' => Fixture::page_size(),
        ]);

        $retrieved_refund = Refund::retrieve($refunds['refunds'][0]);

        $this->assertInstanceOf('\EasyPost\Refund', $retrieved_refund);
        $this->assertEquals($refunds['refunds'][0]->id, $retrieved_refund->id);
    }
}
