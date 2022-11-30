<?php

namespace EasyPost\Test;

use EasyPost\Refund;
use EasyPost\Shipment;

class RefundTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a refund.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('refunds/create.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());
        $retrievedShipment = Shipment::retrieve($shipment); // We need to retrieve the shipment so that the tracking_code has time to populate

        $refund = Refund::create([
            'carrier' => Fixture::usps(),
            'tracking_codes' => $retrievedShipment->tracking_code,
        ]);

        $this->assertStringMatchesFormat('rfnd_%s', $refund[0]->id);
        $this->assertEquals('submitted', $refund[0]->status);
    }

    /**
     * Test retrieving all refunds.
     */
    public function testAll()
    {
        TestUtil::setupCassette('refunds/all.yml');

        $refunds = Refund::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $refundsArray = $refunds['refunds'];

        $this->assertLessThanOrEqual($refundsArray, Fixture::pageSize());
        $this->assertNotNull($refunds['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Refund', $refundsArray);

        // Return so other tests can reuse these objects
        return $refunds;
    }

    /**
     * Test retrieving a refund.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('refunds/retrieve.yml');

        $refunds = Refund::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $retrievedRefund = Refund::retrieve($refunds['refunds'][0]);

        $this->assertInstanceOf('\EasyPost\Refund', $retrievedRefund);
        $this->assertEquals($refunds['refunds'][0]->id, $retrievedRefund->id);
    }
}
