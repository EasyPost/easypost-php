<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Shipment;
use EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));

class ShipmentTest extends \PHPUnit\Framework\TestCase
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
     * Test the creation of a Shipment
     *
     * @return Shipment
     */
    public function testCreate()
    {
        VCR::insertCassette('shipments/create.yml');

        $shipment = Shipment::create(array(
            "to_address" => array(
                "street1"   => "388 Townsend St",
                "street2"   => "Apt 20",
                "city"      => "San Francisco",
                "state"     => "CA",
                "zip"       => "94107",
            ),
            "from_address" => array(
                "street1"   => "388 Townsend St",
                "street2"   => "Apt 20",
                "city"      => "San Francisco",
                "state"     => "CA",
                "zip"       => "94107",
            ),
            "parcel" => array(
                "length"    => "10",
                "width"     => "8",
                "height"    => "4",
                "weight"    => "15",
            )
        ));

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertIsString($shipment->id);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals($shipment->parcel->weight, '15');

        // Return so the `retrieve` test can reuse this object
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
        VCR::insertCassette('shipments/retrieve.yml');

        $retrieved_shipment = Shipment::retrieve($shipment->id);

        $this->assertInstanceOf('\EasyPost\Shipment', $retrieved_shipment);
        $this->assertEquals($retrieved_shipment->id, $shipment->id);
        $this->assertEquals($retrieved_shipment, $shipment);

        // Return so the `buy` test can reuse this object
        return $shipment;
    }

    /**
     * Test buying a Shipment
     *
     * @param Shipment $shipment
     * @return void
     * @depends testRetrieve
     */
    public function testBuy(Shipment $shipment)
    {
        VCR::insertCassette('shipments/buy.yml');

        $shipment->buy(array(
            'id' => 'rate_94ad5814f2be4c9e97dc6256b8ec940a',
        ));

        $this->assertNotNull($shipment->postage_label);
    }

    /**
     * Test retrieving smartrates for a shipment
     *
     * @return void
     */
    public function testSmartrate()
    {
        VCR::insertCassette('shipments/smartrates.yml');

        $shipment = Shipment::create(array(
            "to_address" => array(
                "street1"   => "388 Townsend St",
                "street2"   => "Apt 20",
                "city"      => "San Francisco",
                "state"     => "CA",
                "zip"       => "94107",
            ),
            "from_address" => array(
                "street1"   => "388 Townsend St",
                "street2"   => "Apt 20",
                "city"      => "San Francisco",
                "state"     => "CA",
                "zip"       => "94107",
            ),
            "parcel" => array(
                "length"    => "10",
                "width"     => "8",
                "height"    => "4",
                "weight"    => "15",
            )
        ));

        $this->assertNotNull($shipment->rates);

        $smartrates = $shipment->get_smartrates();
        $this->assertEquals($shipment->rates[0]['id'], $smartrates[0]['id']);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_50'], 1);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_75'], 1);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_85'], 1);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_90'], 2);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_95'], 2);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_97'], 3);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_99'], 4);
    }
}
