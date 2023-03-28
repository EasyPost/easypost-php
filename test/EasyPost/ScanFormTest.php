<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\ScanForm;
use EasyPost\Exception\General\EndOfPaginationException;
use Exception;

class ScanFormTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a scanForm.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('scanForms/create.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $scanForm = self::$client->scanForm->create([
            'shipments' => [$shipment],
        ]);

        $this->assertInstanceOf(ScanForm::class, $scanForm);
        $this->assertStringMatchesFormat('sf_%s', $scanForm->id);
    }

    /**
     * Test retrieving a scanForm.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('scanForms/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $scanForm = self::$client->scanForm->create([
            'shipments' => [$shipment],
        ]);

        $retrievedScanform = self::$client->scanForm->retrieve($scanForm->id);

        $this->assertInstanceOf(ScanForm::class, $retrievedScanform);
        $this->assertEquals($scanForm, $retrievedScanform);
    }

    /**
     * Test retrieving all scanForms.
     */
    public function testAll()
    {
        TestUtil::setupCassette('scanForms/all.yml');

        $scanForms = self::$client->scanForm->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $scanformsArray = $scanForms['scan_forms'];

        $this->assertLessThanOrEqual($scanformsArray, Fixture::pageSize());
        $this->assertNotNull($scanForms['has_more']);
        $this->assertContainsOnlyInstancesOf(ScanForm::class, $scanformsArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('scanForms/getNextPage.yml');

        try {
            $scanforms = self::$client->scanForm->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->scanForm->getNextPage($scanforms, Fixture::pageSize());

            $firstIdOfFirstPage = $scanforms['scan_forms'][0]->id;
            $secondIdOfSecondPage = $nextPage['scan_forms'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
    }
}
