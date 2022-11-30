<?php

namespace EasyPost\Test;

use EasyPost\ScanForm;
use EasyPost\Shipment;

class ScanFormTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a scanform.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('scanforms/create.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $scanform = ScanForm::create([
            'shipments' => [$shipment],
        ]);

        $this->assertInstanceOf('\EasyPost\ScanForm', $scanform);
        $this->assertStringMatchesFormat('sf_%s', $scanform->id);
    }

    /**
     * Test retrieving a scanform.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('scanforms/retrieve.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $scanform = ScanForm::create([
            'shipments' => [$shipment],
        ]);

        $retrievedScanform = ScanForm::retrieve($scanform->id);

        $this->assertInstanceOf('\EasyPost\ScanForm', $retrievedScanform);
        $this->assertEquals($scanform, $retrievedScanform);
    }

    /**
     * Test retrieving all scanforms.
     */
    public function testAll()
    {
        TestUtil::setupCassette('scanforms/all.yml');

        $scanforms = ScanForm::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $scanformsArray = $scanforms['scan_forms'];

        $this->assertLessThanOrEqual($scanformsArray, Fixture::pageSize());
        $this->assertNotNull($scanforms['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\ScanForm', $scanformsArray);
    }
}
