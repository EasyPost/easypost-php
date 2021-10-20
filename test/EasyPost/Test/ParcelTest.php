<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Parcel;
use EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));

class ParcelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test the creation of a Parcel
     *
     * @return Parcel
     */
    public function testCreate()
    {
        VCR::insertCassette('parcels/create.yml');

        $parcel = Parcel::create(array(
            "length"    => "10",
            "width"     => "8",
            "height"    => "4",
            "weight"    => 15.4,
        ));

        $this->assertInstanceOf('\EasyPost\Parcel', $parcel);
        $this->assertIsString($parcel->id);
        $this->assertStringMatchesFormat('prcl_%s', $parcel->id);
        $this->assertEquals($parcel->weight, 15.4);

        // Return so the `retrieve` test can reuse this object
        return $parcel;
    }

    /**
     * Test the retrieval of a Parcel
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
        $this->assertEquals($retrieved_parcel->id, $parcel->id);
        $this->assertEquals($retrieved_parcel, $parcel);
    }
}
