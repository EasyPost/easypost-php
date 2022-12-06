<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;

class RefundTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a refund.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('refunds/create.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());
        $retrievedShipment = self::$client->shipment->retrieve($shipment->id); // We need to retrieve the shipment so that the tracking_code has time to populate

        $refund = self::$client->refund->create([
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

        $refunds = self::$client->refund->all([
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

        $refunds = self::$client->refund->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $retrievedRefund = self::$client->refund->retrieve($refunds['refunds'][0]->id);

        $this->assertInstanceOf('\EasyPost\Refund', $retrievedRefund);
        $this->assertEquals($refunds['refunds'][0]->id, $retrievedRefund->id);
    }
}
