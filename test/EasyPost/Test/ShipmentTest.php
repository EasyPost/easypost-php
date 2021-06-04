<?php

namespace EasyPost\Test;

use EasyPost\Shipment;
use EasyPost\EasyPost;

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

class ShipmentTest extends \PHPUnit\Framework\TestCase
{

    // TODO: set up tests for exceptions and error codes

    /**
     * Test the creation of a Shipment
     *
     * @return void
     */
    public function testCreate()
    {
        $shipment = Shipment::create(
            array(
                "to_address"    => array(
                    "street1" => "388 Townsend St",
                    "street2" => "Apt 20",
                    "city"    => "San Francisco",
                    "state"   => "CA",
                    "zip"     => "94107"),
                "from_address"  => array(
                    "street1" => "388 Townsend St",
                    "street2" => "Apt 20",
                    "city"    => "San Francisco",
                    "state"   => "CA",
                    "zip"     => "94107"),
                "parcel"    => array(
                    "length"     => "10",
                    "width"     => "8",
                    "height"    => "4",
                    "weight"    => "15")
            )
        );
        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertIsString($shipment->id);
        $this->assertStringMatchesFormat("shp_%s", $shipment->id);

        return $shipment;
    }


    /**
     * Test the retrieval of a Shipment
     *
     * @param Shipment $shipment
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Shipment $shipment)
    {
        $retrieved_shipment = Shipment::retrieve($shipment->id);

        $this->assertInstanceOf('\EasyPost\Shipment', $retrieved_shipment);
        $this->assertEquals($retrieved_shipment->id, $shipment->id);
        $this->assertEquals($retrieved_shipment, $shipment);

        return $retrieved_shipment;
    }


    /**
     * Test retrieving smartrates for a shipment
     *
     * @return void
     */
    public function testSmartrate()
    {
        $shipment = Shipment::create(
            array(
                "to_address"    => array(
                    "street1" => "388 Townsend St",
                    "street2" => "Apt 20",
                    "city"    => "San Francisco",
                    "state"   => "CA",
                    "zip"     => "94107"),
                "from_address"  => array(
                    "street1" => "388 Townsend St",
                    "street2" => "Apt 20",
                    "city"    => "San Francisco",
                    "state"   => "CA",
                    "zip"     => "94107"),
                "parcel"    => array(
                    "length"     => "10",
                    "width"     => "8",
                    "height"    => "4",
                    "weight"    => "15")
            )
        );
        $this->assertNotNull($shipment->rates);

        $smartrates = $shipment->get_smartrates();
        $this->assertEquals($shipment->rates[0]['id'], $smartrates[0]['id']);
        # TODO: Once we've added `php-vcr`, assert that the following values actually match an integer
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_50']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_75']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_85']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_90']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_95']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_97']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_99']);
    }
}
