<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Parcel;
use EasyPost\Test\Fixture;
use VCR\VCR;

class ParcelTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Parcel.
     */
    public function testCreate()
    {
        VCR::insertCassette('parcels/create.yml');

        $parcel = Parcel::create(Fixture::basicParcel());

        $this->assertInstanceOf('\EasyPost\Parcel', $parcel);
        $this->assertStringMatchesFormat('prcl_%s', $parcel->id);
        $this->assertEquals(15.4, $parcel->weight);
    }

    /**
     * Test retrieving a Parcel.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('parcels/retrieve.yml');

        $parcel = Parcel::create(Fixture::basicParcel());

        $retrievedParcel = Parcel::retrieve($parcel->id);

        $this->assertInstanceOf('\EasyPost\Parcel', $retrievedParcel);
        $this->assertEquals($parcel, $retrievedParcel);
    }
}
