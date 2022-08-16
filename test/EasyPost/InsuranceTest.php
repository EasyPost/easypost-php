<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Insurance;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

class InsuranceTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an insurance object.
     */
    public function testCreate()
    {
        VCR::insertCassette('insurance/create.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $insuranceData = Fixture::basicInsurance();
        $insuranceData['tracking_code'] = $shipment->tracking_code;

        $insurance = Insurance::create($insuranceData);

        $this->assertInstanceOf('\EasyPost\Insurance', $insurance);
        $this->assertStringMatchesFormat('ins_%s', $insurance->id);
        $this->assertEquals('100.00000', $insurance->amount);
    }

    /**
     * Test retrieving an insurance object.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('insurance/retrieve.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $insuranceData = Fixture::basicInsurance();
        $insuranceData['tracking_code'] = $shipment->tracking_code;

        $insurance = Insurance::create($insuranceData);

        $retrievedInsurance = Insurance::retrieve($insurance->id);

        $this->assertInstanceOf('\EasyPost\Insurance', $retrievedInsurance);
        $this->assertEquals($insurance, $retrievedInsurance);
    }

    /**
     * Test retrieving all insurance.
     */
    public function testAll()
    {
        VCR::insertCassette('insurance/all.yml');

        $insurance = Insurance::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $insuranceArray = $insurance['insurances'];

        $this->assertLessThanOrEqual($insuranceArray, Fixture::pageSize());
        $this->assertNotNull($insurance['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Insurance', $insuranceArray);
    }
}
