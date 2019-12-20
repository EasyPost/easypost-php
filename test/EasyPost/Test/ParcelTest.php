<?php

namespace EasyPost\Test;

use EasyPost\Parcel;
use EasyPost\EasyPost;

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

class ParcelTest extends \PHPUnit\Framework\TestCase
{

    // TODO: set up tests for exceptions and error codes

    /**
     * Test the creation of a Parcel
     *
     * @return void
     */
    public function testCreate()
    {
        $parcel_params = array("length"     => "10",
                                "width"     => "8",
                                "height"    => "4",
                                "weight"    => "15");
        $parcel = Parcel::create($parcel_params);
        $this->assertInstanceOf('\EasyPost\Parcel', $parcel);
        $this->assertIsString($parcel->id);
        $this->assertStringMatchesFormat("prcl_%s", $parcel->id);

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
        $retrieved_parcel = Parcel::retrieve($parcel->id);

        $this->assertInstanceOf('\EasyPost\Parcel', $retrieved_parcel);
        $this->assertEquals($retrieved_parcel->id, $parcel->id);
        $this->assertEquals($retrieved_parcel, $parcel);

        return $retrieved_parcel;
    }
}
