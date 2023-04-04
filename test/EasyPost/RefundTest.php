<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Refund;
use Exception;

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
        // We need to retrieve the shipment so that the tracking_code has time to populate
        $retrievedShipment = self::$client->shipment->retrieve($shipment->id);

        $refund = self::$client->refund->create([
            'carrier' => Fixture::usps(),
            'tracking_codes' => [$retrievedShipment->tracking_code],
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
        $this->assertContainsOnlyInstancesOf(Refund::class, $refundsArray);

        // Return so other tests can reuse these objects
        return $refunds;
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('refunds/getNextPage.yml');

        try {
            $refunds = self::$client->refund->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->refund->getNextPage($refunds, Fixture::pageSize());

            $firstIdOfFirstPage = $refunds['refunds'][0]->id;
            $secondIdOfSecondPage = $nextPage['refunds'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
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

        $this->assertInstanceOf(Refund::class, $retrievedRefund);
        $this->assertEquals($refunds['refunds'][0]->id, $retrievedRefund->id);
    }
}
