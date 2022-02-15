<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Parcel;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class ParcelTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Parcel.
     *
     * @return Parcel
     */
    public function testCreate()
    {
        VCR::insertCassette('parcels/create.yml');

        $parcel = Parcel::create(Fixture::basic_parcel());

        $this->assertInstanceOf('\EasyPost\Parcel', $parcel);
        $this->assertStringMatchesFormat('prcl_%s', $parcel->id);
        $this->assertEquals(15.4, $parcel->weight);

        // Return so other tests can reuse this object
        return $parcel;
    }

    /**
     * Test retrieving a Parcel.
     *
     * @param Parcel $parcel
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Parcel $parcel)
    {
        VCR::insertCassette('parcels/retrieve.yml');

        $retrieved_parcel = Parcel::retrieve($parcel->id);

        $this->assertInstanceOf('\EasyPost\Parcel', $retrieved_parcel);
        $this->assertEquals($parcel, $retrieved_parcel);
    }
}
