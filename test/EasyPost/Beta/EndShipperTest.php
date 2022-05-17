<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\EasyPost;
use EasyPost\Beta\EndShipper;

class EndShipperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

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
     * Test creating an EndShipper.
     *
     * @return EndShipper
     */
    public function testCreate()
    {
        VCR::insertCassette('end_shipper/create.yml');

        $end_shipper = EndShipper::create(Fixture::end_shipper_address());

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $end_shipper);
        $this->assertStringMatchesFormat('es_%s', $end_shipper->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $end_shipper->street1);

        return $end_shipper;
    }

    /**
     * Test retrieving an EndShipper.
     *
     * @depends testCreate
     * @return EndShipper
     */
    public function testRetrieve(EndShipper $end_shipper)
    {
        VCR::insertCassette('end_shipper/retrieve.yml');

        $retrieved_end_shipper = EndShipper::retrieve($end_shipper->id);

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $retrieved_end_shipper);
        $this->assertEquals($end_shipper->street1, $retrieved_end_shipper->street1);

        return $retrieved_end_shipper;
    }

    /**
     * Test retrieving all EndShippers.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('end_shipper/all.yml');

        $end_shipper = EndShipper::all([
            'page_size' => Fixture::page_size(),
        ]);

        $this->assertLessThanOrEqual($end_shipper, Fixture::page_size());
        $this->assertContainsOnlyInstancesOf('\EasyPost\Beta\EndShipper', $end_shipper);
    }

    /**
     * Test updating an EndShipper.
     *
     * @depends testCreate
     * @return EndShipper
     */
    public function testUpdate(EndShipper $end_shipper)
    {
        VCR::insertCassette('end_shipper/update.yml');

        $end_shipper->name = 'Jack Sparrow';
        $end_shipper->company = 'EasyPost';
        $end_shipper->street1 = '388 Townsend St';
        $end_shipper->street2 = 'Apt 20';
        $end_shipper->city = 'San Francisco';
        $end_shipper->state = 'CA';
        $end_shipper->zip = '94107';
        $end_shipper->country = 'US';
        $end_shipper->phone = '9999999999';
        $end_shipper->email = 'test@example.com';
        $end_shipper->save();

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $end_shipper);
        $this->assertStringMatchesFormat('es_%s', $end_shipper->id);
        $this->assertEquals('9999999999', $end_shipper->phone);
    }
}
