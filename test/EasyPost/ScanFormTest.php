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
     * Test creating a scanform.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('scanforms/create.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $scanform = ScanForm::create([
            'shipments' => [$shipment],
        ]);

        $this->assertInstanceOf('\EasyPost\ScanForm', $scanform);
        $this->assertStringMatchesFormat('sf_%s', $scanform->id);
    }

    /**
     * Test retrieving a scanform.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('scanforms/retrieve.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $scanform = ScanForm::create([
            'shipments' => [$shipment],
        ]);

        $retrieved_scanform = ScanForm::retrieve($scanform->id);

        $this->assertInstanceOf('\EasyPost\ScanForm', $retrieved_scanform);
        $this->assertEquals($scanform, $retrieved_scanform);
    }

    /**
     * Test retrieving all scanforms.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('scanforms/all.yml');

        $scanforms = ScanForm::all([
            'page_size' => Fixture::page_size(),
        ]);

        $scanforms_array = $scanforms['scan_forms'];

        $this->assertLessThanOrEqual($scanforms_array, Fixture::page_size());
        $this->assertNotNull($scanforms['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\ScanForm', $scanforms_array);
    }
}
