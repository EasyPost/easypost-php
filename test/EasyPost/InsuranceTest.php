<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Insurance;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class InsuranceTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an insurance object.
     *
     * @return Insurance
     */
    public function testCreate()
    {
        VCR::insertCassette('insurance/create.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $insurance = Insurance::create([
            'to_address' => Fixture::basic_address(),
            'from_address' => Fixture::basic_address(),
            'tracking_code' => $shipment->tracking_code,
            'carrier' => Fixture::usps(),
            'amount' => '100',
        ]);

        $this->assertInstanceOf('\EasyPost\Insurance', $insurance);
        $this->assertStringMatchesFormat('ins_%s', $insurance->id);
        $this->assertEquals('100.00000', $insurance->amount);

        // Return so other tests can reuse this object
        return $insurance;
    }

    /**
     * Test retrieving an insurance object.
     *
     * @param object $insurance
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(object $insurance)
    {
        VCR::insertCassette('insurance/retrieve.yml');

        $retrieved_insurance = Insurance::retrieve($insurance->id);

        $this->assertInstanceOf('\EasyPost\Insurance', $retrieved_insurance);
        $this->assertEquals($insurance, $retrieved_insurance);
    }

    /**
     * Test retrieving all insurance.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('insurance/all.yml');

        $insurance = Insurance::all([
            'page_size' => Fixture::page_size(),
        ]);

        $insurance_array = $insurance['insurances'];

        $this->assertLessThanOrEqual($insurance_array, Fixture::page_size());
        $this->assertNotNull($insurance['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Insurance', $insurance_array);
    }
}
