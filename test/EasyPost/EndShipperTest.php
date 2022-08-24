<?php

namespace EasyPost\Test;

use EasyPost\EndShipper;
use EasyPost\EasyPost;
use VCR\VCR;

class EndShipperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

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
     * Test creating an EndShipper.
     */
    public function testCreate()
    {
        VCR::insertCassette('end_shipper/create.yml');

        $endShipper = EndShipper::create(Fixture::caAddress1());

        $this->assertInstanceOf('\EasyPost\EndShipper', $endShipper);
        $this->assertStringMatchesFormat('es_%s', $endShipper->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $endShipper->street1);
    }

    /**
     * Test retrieving an EndShipper.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('end_shipper/retrieve.yml');

        $endShipper = EndShipper::create(Fixture::caAddress1());

        $retrievedEndShipper = EndShipper::retrieve($endShipper->id);

        $this->assertInstanceOf('\EasyPost\EndShipper', $retrievedEndShipper);
        $this->assertEquals($endShipper->street1, $retrievedEndShipper->street1);
    }

    /**
     * Test retrieving all EndShippers.
     */
    public function testAll()
    {
        VCR::insertCassette('end_shipper/all.yml');

        $endShippers = EndShipper::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $endShipperArray = $endShippers['end_shippers'];

        $this->assertLessThanOrEqual($endShipperArray, Fixture::pageSize());
        $this->assertNotNull($endShippers['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\EndShipper', $endShipperArray);
    }

    /**
     * Test updating an EndShipper.
     */
    public function testUpdate()
    {
        VCR::insertCassette('end_shipper/update.yml');

        $endShipper = EndShipper::create(Fixture::caAddress1());

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

        $this->assertInstanceOf('\EasyPost\EndShipper', $endShipper);
        $this->assertStringMatchesFormat('es_%s', $endShipper->id);
        $this->assertEquals($newName, $endShipper->name);
    }
}
