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
            ),
            "customs_info"  => array(
                "eel_pfc" => 'NOEEI 30.37(a)',
                "customs_certify" => true,
                "customs_signer" => 'Steve Brule',
                "contents_type" => 'merchandise',
                "contents_explanation" => '',
                "restriction_type" => 'none',
                "non_delivery_option" => 'return',
                "customs_items" => array(
                    array(
                        "description" => 'Sweet shirts',
                        "quantity" => 2,
                        "weight" => 11,
                        "value" => 23,
                        "hs_tariff_number" => '654321',
                        "origin_country" => 'US'
                    ),
                ),
            ),
            "options" => array(
                "label_format"      => "PDF",
                "invoice_number"    => 123 // Tests that we encode integers to strings where appropriate
            ),
            "reference" => 123 // Tests that we encode integers to strings where appropriate
        ));

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertIsString($shipment->id);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals($shipment->options->label_format, 'PDF');
        $this->assertEquals($shipment->options->invoice_number, '123');
        $this->assertEquals($shipment->reference, '123');

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
            'rate' => $shipment->lowest_rate(),
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
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_85'], 2);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_90'], 2);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_95'], 2);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_97'], 3);
        $this->assertEquals($smartrates[0]['time_in_transit']['percentile_99'], 3);
    }

    /**
     * Test the creation of a Shipment with empty or null objects and arrays
     *
     * @return void
     */
    public function testCreateEmptyObjects()
    {
        VCR::insertCassette('shipments/createEmptyObjects.yml');

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
                "length"    => null,
                "width"     => "",
                "height"    => null,
                "weight"    => "15",
            ),
            "customs_info" => array(
                "customs_items" => array()
            ),
            "options" => null,
            "tax_identifiers" => null,
            "reference" => "",
        ));

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertIsString($shipment->id);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertNotEmpty($shipment->options); // The EasyPost API populates some default values here
        $this->assertEmpty($shipment->customs_info);
        $this->assertNull($shipment->reference);
    }

    /**
     * Test the creation of a Shipment with `tax_identifiers`
     *
     * @return void
     */
    public function testCreateTaxIdentifiers()
    {
        VCR::insertCassette('shipments/createTaxIdentifiers.yml');

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
            ),
            "tax_identifiers" => array(
                array(
                    "entity" => "SENDER",
                    "tax_id_type" => "IOSS",
                    "tax_id" => "12345",
                    "issuing_country" => "GB",
                )
            )
        ));

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertIsString($shipment->id);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals($shipment->tax_identifiers[0]['tax_id_type'], 'IOSS');
    }
}
