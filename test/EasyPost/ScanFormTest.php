<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\ScanForm;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

class ScanFormTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a scanform.
     */
    public function testCreate()
    {
        VCR::insertCassette('scanforms/create.yml');

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
        VCR::insertCassette('scanforms/retrieve.yml');

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
        VCR::insertCassette('scanforms/all.yml');

        $scanforms = ScanForm::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $scanformsArray = $scanforms['scan_forms'];

        $this->assertLessThanOrEqual($scanformsArray, Fixture::pageSize());
        $this->assertNotNull($scanforms['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\ScanForm', $scanformsArray);
    }
}
