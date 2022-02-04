<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\ScanForm;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class ScanFormTest extends \PHPUnit\Framework\TestCase
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

        // Return so the `retrieve` test can reuse this object
        return $scanform;
    }

    /**
     * Test retrieving a scanform.
     *
     * @param ScanForm $scanform
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(ScanForm $scanform)
    {
        VCR::insertCassette('scanforms/retrieve.yml');

        $retrieved_scanform = ScanForm::retrieve($scanform->id);

        $this->assertInstanceOf('\EasyPost\ScanForm', $retrieved_scanform);
        $this->assertStringMatchesFormat('sf_%s', $retrieved_scanform->id);
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
        foreach ($scanforms_array as $scanform) {
            $this->assertInstanceOf('\EasyPost\ScanForm', $scanform);
        }

        // Return so other tests can reuse these objects
        return $scanforms;
    }
}
