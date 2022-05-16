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
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('insurance/create.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $insurance_data = Fixture::basic_insurance();
        $insurance_data['tracking_code'] = $shipment->tracking_code;

        $insurance = Insurance::create($insurance_data);

        $this->assertInstanceOf('\EasyPost\Insurance', $insurance);
        $this->assertStringMatchesFormat('ins_%s', $insurance->id);
        $this->assertEquals('100.00000', $insurance->amount);
    }

    /**
     * Test retrieving an insurance object.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('insurance/retrieve.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $insurance_data = Fixture::basic_insurance();
        $insurance_data['tracking_code'] = $shipment->tracking_code;

        $insurance = Insurance::create($insurance_data);

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
