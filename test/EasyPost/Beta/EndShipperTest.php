<?php

namespace EasyPost\Test;

use EasyPost\Beta\EndShipper;
use EasyPost\EasyPost;
use VCR\VCR;

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

        $endShipper = EndShipper::create(Fixture::end_shipper_address());

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $endShipper);
        $this->assertStringMatchesFormat('es_%s', $endShipper->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $endShipper->street1);

        return $endShipper;
    }

    /**
     * Test retrieving an EndShipper.
     *
     * @depends testCreate
     * @return EndShipper
     */
    public function testRetrieve(EndShipper $endShipper)
    {
        VCR::insertCassette('end_shipper/retrieve.yml');

        $retrievedEndShipper = EndShipper::retrieve($endShipper->id);

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $retrievedEndShipper);
        $this->assertEquals($endShipper->street1, $retrievedEndShipper->street1);

        return $retrievedEndShipper;
    }

    /**
     * Test retrieving all EndShippers.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('end_shipper/all.yml');

        $endShipper = EndShipper::all([
            'page_size' => Fixture::page_size(),
        ]);

        $this->assertLessThanOrEqual($endShipper, Fixture::page_size());
        $this->assertContainsOnlyInstancesOf('\EasyPost\Beta\EndShipper', $endShipper);
    }

    /**
     * Test updating an EndShipper.
     *
     * @depends testCreate
     * @return EndShipper
     */
    public function testUpdate(EndShipper $endShipper)
    {
        VCR::insertCassette('end_shipper/update.yml');

        // All caps because API will return all caps as part of verification.
        $newName = 'NEW NAME';

        $endShipper->name = $newName;
        $endShipper->company = 'EasyPost';
        $endShipper->street1 = '388 Townsend St';
        $endShipper->street2 = 'Apt 20';
        $endShipper->city = 'San Francisco';
        $endShipper->state = 'CA';
        $endShipper->zip = '94107';
        $endShipper->country = 'US';
        $endShipper->phone = '9999999999';
        $endShipper->email = 'test@example.com';
        $endShipper->save();

        $this->assertInstanceOf('\EasyPost\Beta\EndShipper', $endShipper);
        $this->assertStringMatchesFormat('es_%s', $endShipper->id);
        $this->assertEquals($newName, $endShipper->name);
    }
}
