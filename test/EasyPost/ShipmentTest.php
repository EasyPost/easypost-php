<?php

namespace EasyPost\Test;

use EasyPost\Address;
use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Parcel;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class ShipmentTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Shipment.
     */
    public function testCreate()
    {
        VCR::insertCassette('shipments/create.yml');

        $shipment = Shipment::create(Fixture::fullShipment());

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertNotNull($shipment->rates);
        $this->assertEquals('PNG', $shipment->options->label_format);
        $this->assertEquals('123', $shipment->options->invoice_number);
        $this->assertEquals('123', $shipment->reference);
    }

    /**
     * Test retrieving a Shipment.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('shipments/retrieve.yml');

        $shipment = Shipment::create(Fixture::fullShipment());

        $retrievedShipment = Shipment::retrieve($shipment->id);

        $this->assertInstanceOf('\EasyPost\Shipment', $retrievedShipment);
        $this->assertEquals($shipment->id, $retrievedShipment->id);
    }

    /**
     * Test retrieving all shipments.
     */
    public function testAll()
    {
        VCR::insertCassette('shipments/all.yml');

        $shipments = Shipment::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $shipmentsArray = $shipments['shipments'];

        $this->assertLessThanOrEqual($shipmentsArray, Fixture::pageSize());
        $this->assertNotNull($shipments['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Shipment', $shipmentsArray);
    }

    /**
     * Test buying a Shipment.
     */
    public function testBuy()
    {
        VCR::insertCassette('shipments/buy.yml');

        $shipment = Shipment::create(Fixture::fullShipment());

        $shipment->buy([
            'rate' => $shipment->lowest_rate(),
        ]);

        $this->assertNotNull($shipment->postage_label);
    }

    /**
     * Test regenerating rates for a shipment.
     */
    public function testRegenerateRates()
    {
        VCR::insertCassette('shipments/regenerateRates.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $rates = $shipment->regenerate_rates();

        $ratesArray = $rates['rates'];

        $this->assertIsArray($ratesArray);
        foreach ($ratesArray as $rate) {
            $this->assertInstanceOf('\EasyPost\Rate', $rate);
        }
    }

    /**
     * Test converting the label format of a Shipment.
     */
    public function testConvertLabel()
    {
        VCR::insertCassette('shipments/convertLabel.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

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
     */
    public function testInsure()
    {
        VCR::insertCassette('shipments/insure.yml');

        $shipmentData = Fixture::oneCallBuyShipment();
        // Set to 0 so USPS doesn't insure this automatically and we can insure the shipment manually
        $shipmentData['insurance'] = 0;

        $shipment = Shipment::create($shipmentData);

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
     */
    public function testRefund()
    {
        VCR::insertCassette('shipments/refund.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $shipment->refund();

        $this->assertEquals('submitted', $shipment->refund_status);
    }

    /**
     * Test retrieving smartrates for a shipment.
     */
    public function testSmartrate()
    {
        VCR::insertCassette('shipments/smartrates.yml');

        $shipment = Shipment::create(Fixture::basicShipment());

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
     */
    public function testCreateEmptyObjects()
    {
        VCR::insertCassette('shipments/createEmptyObjects.yml');

        $shipmentData = Fixture::basicShipment();
        $shipmentData['customs_info']['customs_items'] = [];
        $shipmentData['options'] = null;
        $shipmentData['tax_identifiers'] = null;
        $shipmentData['reference'] = '';

        $shipment = Shipment::create($shipmentData);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertNotEmpty($shipment->options); // The EasyPost API populates some default values here
        $this->assertEmpty($shipment->customs_info);
        $this->assertNull($shipment->reference);
    }

    /**
     * Test creating a Shipment with `tax_identifiers`.
     */
    public function testCreateTaxIdentifiers()
    {
        VCR::insertCassette('shipments/createTaxIdentifiers.yml');

        $shipmentData = Fixture::basicShipment();
        $shipmentData['tax_identifiers'] = [Fixture::taxIdentifier()];

        $shipment = Shipment::create($shipmentData);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals('IOSS', $shipment->tax_identifiers[0]['tax_id_type']);
    }

    /**
     * Test creating a Shipment when only IDs are used.
     */
    public function testCreateWithIds()
    {
        VCR::insertCassette('shipments/createWithIds.yml');

        $fromAddress = Address::create(Fixture::caAddress1());
        $toAddress = Address::create(Fixture::caAddress1());
        $parcel = Parcel::create(Fixture::basicParcel());

        $shipment = Shipment::create([
            'from_address' => ['id' => $fromAddress->id],
            'to_address' => ['id' => $toAddress->id],
            'parcel' => ['id' => $parcel->id],
        ]);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertStringMatchesFormat('adr_%s', $shipment->from_address->id);
        $this->assertStringMatchesFormat('adr_%s', $shipment->to_address->id);
        $this->assertStringMatchesFormat('prcl_%s', $shipment->parcel->id);
        $this->assertEquals('388 Townsend St', $shipment->from_address->street1);
    }

    /**
     * Test various usage alterations of the lowest_rate method.
     */
    public function testLowestRate()
    {
        VCR::insertCassette('shipments/lowestRate.yml');

        $shipment = Shipment::create(Fixture::fullShipment());

        // Test lowest rate with no filters
        $lowestRate = $shipment->lowest_rate();
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.57', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with service filter (this rate is higher than the lowest but should filter)
        $lowestRate = $shipment->lowest_rate([], ['Priority']);
        $this->assertEquals('Priority', $lowestRate['service']);
        $this->assertEquals('7.90', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $shipment->lowest_rate(['BAD CARRIER'], []);
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }

    /**
     * Test various usage alterations of the lowest_smartrate method.
     */
    public function testLowestSmartrate()
    {
        VCR::insertCassette('shipments/lowestSmartrate.yml');

        $shipment = Shipment::create(Fixture::fullShipment());

        // Test lowest rate with no filters
        $lowestRate = $shipment->lowest_smartrate(2, 'percentile_90');
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals(5.57, $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest smartrate with invalid filters (should error due to strict delivery_days)
        try {
            $lowestRate = $shipment->lowest_smartrate(0, 'percentile_90');
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowest smartrate with invalid filters (should error due to invalid delivery_accuracy)
        try {
            $lowestRate = $shipment->lowest_rate(3, 'BAD_ACCURACY');
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }

    /**
     * Test various usage alterations of the get_lowest_smartrate method.
     */
    public function testGetLowestSmartrate()
    {
        VCR::insertCassette('shipments/getLowestSmartrate.yml');

        $shipment = Shipment::create(Fixture::fullShipment());
        $smartrates = $shipment->get_smartrates();

        // Test lowest smartrate with valid filters
        $lowestSmartrate = Shipment::get_lowest_smartrate($smartrates, 2, 'percentile_90');
        $this->assertEquals('First', $lowestSmartrate['service']);
        $this->assertEquals(5.57, $lowestSmartrate['rate']);
        $this->assertEquals('USPS', $lowestSmartrate['carrier']);

        // Test lowest smartrate with invalid filters (should error due to strict delivery_days)
        try {
            $lowestSmartrate = Shipment::get_lowest_smartrate($smartrates, 0, 'percentile_90');
        } catch (Error $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowest smartrate with invalid filters (should error due to invalid delivery_accuracy)
        try {
            $lowestSmartrate = Shipment::get_lowest_smartrate($smartrates, 3, 'BAD_ACCURACY');
        } catch (Error $error) {
            $this->assertEquals('Invalid delivery_accuracy value, must be one of: ["percentile_50","percentile_75","percentile_85","percentile_90","percentile_95","percentile_97","percentile_99"]', $error->getMessage());
        }
    }

    /**
     * Tests generating a form for a shipment.
     *
     * @throws Error
     */
    public function testGenerateForm()
    {
        VCR::insertCassette('shipments/generateForm.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $formType = 'return_packing_slip';
        $shipment->generate_form(
            $formType,
            Fixture::rmaFormOtions()
        );

        $this->assertEquals(1, count($shipment->forms));

        $form = $shipment->forms[0];

        $this->assertEquals($formType, $form->form_type);
        $this->assertNotNull($form->form_url);
    }

    /**
     * Tests creating a carbon offset shipment.
     *
     */
    public function testCreatebasicShipment()
    {
        VCR::insertCassette('shipments/createCarbonOffsetShipment.yml');

        $shipment = Shipment::create(Fixture::basicShipment(), null, true);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);

        foreach ($shipment->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }

    /**
     * Tests buying a carbon offset shipment.
     *
     */
    public function testBuybasicShipment()
    {
        VCR::insertCassette('shipments/buyCarbonOffsetShipment.yml');

        $shipment = Shipment::create(Fixture::basicShipment());

        $shipment->buy(
            [
                'rate' => $shipment->lowest_rate(),
            ],
            true,
        );

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);

        $foundCarbonOffset = false;

        foreach ($shipment->fees as $fee) {
            if ($fee->type == 'CarbonOffsetFee') {
                $foundCarbonOffset = true;
            }
        }

        $this->assertTrue($foundCarbonOffset);
    }

    /**
     * Tests one call buy a carbon offset shipment.
     *
     */
    public function testOneCallBuybasicShipment()
    {
        VCR::insertCassette('shipments/oneCallBuyCarbonOffsetShipment.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment(), null, true);

        $this->assertInstanceOf('\EasyPost\Shipment', $shipment);

        foreach ($shipment->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }

    /**
     * Tests rerate a shipment with carbon offset.
     *
     */
    public function testRerateShipmentWithCarbonOffset()
    {
        VCR::insertCassette('shipments/rerateCarbonOffsetShipment.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $newCarbonOffset = $shipment->regenerate_rates(null, true);
        foreach ($newCarbonOffset->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }
}
