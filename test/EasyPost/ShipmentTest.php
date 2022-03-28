<?php

namespace EasyPost\Test;

use EasyPost\Address;
use EasyPost\EasyPost;
use EasyPost\Parcel;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class ShipmentTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Shipment.
     *
     * @return Shipment
     */
    public function testCreate()
    {
        VCR::insertCassette('shipments/create.yml');

        $shipment = Shipment::create(Fixture::full_shipment());

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertNotNull($shipment->rates);
        $this->assertEquals('PNG', $shipment->options->label_format);
        $this->assertEquals('123', $shipment->options->invoice_number);
        $this->assertEquals('123', $shipment->reference);

        // Return so other tests can reuse this object
        return $shipment;
    }

    /**
     * Test retrieving a Shipment.
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
        $this->assertEquals($shipment->id, $retrieved_shipment->id);
    }

    /**
     * Test retrieving all shipments.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('shipments/all.yml');

        $shipments = Shipment::all([
            'page_size' => Fixture::page_size(),
        ]);

        $shipments_array = $shipments['shipments'];

        $this->assertLessThanOrEqual($shipments_array, Fixture::page_size());
        $this->assertNotNull($shipments['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Shipment', $shipments_array);
    }

    /**
     * Test buying a Shipment.
     *
     * @param Shipment $shipment
     * @return Shipment
     * @depends testCreate
     */
    public function testBuy(Shipment $shipment)
    {
        VCR::insertCassette('shipments/buy.yml');

        $shipment->buy([
            'rate' => $shipment->lowest_rate(),
        ]);

        $this->assertNotNull($shipment->postage_label);

        // Return so other tests can reuse this object
        return $shipment;
    }

    /**
     * Test regenerating rates for a shipment.
     *
     * @param Shipment $shipment
     * @return void
     * @depends testCreate
     */
    public function testRegenerateRates(Shipment $shipment)
    {
        VCR::insertCassette('shipments/regenerateRates.yml');

        $rates = $shipment->regenerate_rates();

        $rates_array = $rates['rates'];

        $this->assertIsArray($rates_array);
        foreach ($rates_array as $rate) {
            $this->assertInstanceOf('\EasyPost\Rate', $rate);
        }
    }

    /**
     * Test converting the label format of a Shipment.
     *
     * @param Shipment $shipment
     * @return void
     * @depends testBuy
     */
    public function testConvertLabel(Shipment $shipment)
    {
        VCR::insertCassette('shipments/convertLabel.yml');

        $shipment->label([
            'file_format' => 'ZPL',
        ]);

        $this->assertNotNull($shipment->postage_label->label_zpl_url);
    }

    /**
     * Test insuring a Shipment.
     *
     * If the shipment was purchased with a USPS rate, it must have had its insurance set to `0` when bought
     * so that USPS doesn't automatically insure it so we could manually insure it here.
     *
     * @return void
     */
    public function testInsure()
    {
        VCR::insertCassette('shipments/insure.yml');

        $shipment_data = Fixture::one_call_buy_shipment();
        // Set to 0 so USPS doesn't insure this automatically and we can insure the shipment manually
        $shipment_data['insurance'] = 0;

        $shipment = Shipment::create($shipment_data);

        $shipment->insure([
            'amount' => '100',
        ]);

        $this->assertEquals('100.00', $shipment->insurance);
    }

    /**
     * Test refunding a Shipment.
     *
     * Refunding a test shipment must happen within seconds of the shipment being created as test shipments naturally
     * follow a flow of created -> delivered to cycle through tracking events in test mode - as such anything older
     * than a few seconds in test mode may not be refundable.
     *
     * @return void
     */
    public function testRefund()
    {
        VCR::insertCassette('shipments/refund.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $shipment->refund();

        $this->assertEquals('submitted', $shipment->refund_status);
    }

    /**
     * Test retrieving smartrates for a shipment.
     *
     * @return void
     */
    public function testSmartrate()
    {
        VCR::insertCassette('shipments/smartrates.yml');

        $shipment = Shipment::create(Fixture::basic_shipment());

        $this->assertNotNull($shipment->rates);

        $smartrates = $shipment->get_smartrates();
        $this->assertEquals($shipment->rates[0]['id'], $smartrates[0]['id']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_50']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_75']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_85']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_90']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_95']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_97']);
        $this->assertNotNull($smartrates[0]['time_in_transit']['percentile_99']);
    }

    /**
     * Test creating a Shipment with empty or null objects and arrays.
     *
     * @return void
     */
    public function testCreateEmptyObjects()
    {
        VCR::insertCassette('shipments/createEmptyObjects.yml');

        $shipment_data = Fixture::basic_shipment();
        $shipment_data['customs_info']['customs_items'] = [];
        $shipment_data['options'] = null;
        $shipment_data['tax_identifiers'] = null;
        $shipment_data['reference'] = '';

        $shipment = Shipment::create($shipment_data);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertNotEmpty($shipment->options); // The EasyPost API populates some default values here
        $this->assertEmpty($shipment->customs_info);
        $this->assertNull($shipment->reference);
        $this->assertNull($shipment->tax_identifiers);
    }

    /**
     * Test creating a Shipment with `tax_identifiers`.
     *
     * @return void
     */
    public function testCreateTaxIdentifiers()
    {
        VCR::insertCassette('shipments/createTaxIdentifiers.yml');

        $shipment_data = Fixture::basic_shipment();
        $shipment_data['tax_identifiers'] = [Fixture::tax_identifier()];

        $shipment = Shipment::create($shipment_data);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals('IOSS', $shipment->tax_identifiers[0]['tax_id_type']);
    }

    /**
     * Test creating a Shipment when only IDs are used.
     *
     * @return void
     */
    public function testCreateWithIds()
    {
        VCR::insertCassette('shipments/createWithIds.yml');

        $from_address = Address::create(Fixture::basic_address());
        $to_address = Address::create(Fixture::basic_address());
        $parcel = Parcel::create(Fixture::basic_parcel());

        $shipment = Shipment::create([
            'from_address' => ['id' => $from_address->id],
            'to_address' => ['id' => $to_address->id],
            'parcel' => ['id' => $parcel->id],
        ]);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertStringMatchesFormat('adr_%s', $shipment->from_address->id);
        $this->assertStringMatchesFormat('adr_%s', $shipment->to_address->id);
        $this->assertStringMatchesFormat('prcl_%s', $shipment->parcel->id);
        $this->assertEquals('388 Townsend St', $shipment->from_address->street1);
    }
}
