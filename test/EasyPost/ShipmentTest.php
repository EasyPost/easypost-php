<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Exception\General\FilteringException;
use EasyPost\Exception\General\InvalidParameterException;
use EasyPost\Rate;
use EasyPost\Shipment;
use EasyPost\Util\Util;
use Exception;

class ShipmentTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a Shipment.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('shipments/create.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        $this->assertInstanceOf(Shipment::class, $shipment);
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
        TestUtil::setupCassette('shipments/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        $retrievedShipment = self::$client->shipment->retrieve($shipment->id);

        $this->assertInstanceOf(Shipment::class, $retrievedShipment);
        $this->assertEquals($shipment->id, $retrievedShipment->id);
    }

    /**
     * Test retrieving all shipments.
     */
    public function testAll()
    {
        TestUtil::setupCassette('shipments/all.yml');

        $shipments = self::$client->shipment->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $shipmentsArray = $shipments['shipments'];

        $this->assertLessThanOrEqual($shipmentsArray, Fixture::pageSize());
        $this->assertNotNull($shipments['has_more']);
        $this->assertContainsOnlyInstancesOf(Shipment::class, $shipmentsArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('shipments/getNextPage.yml');

        try {
            $shipments = self::$client->shipment->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->shipment->getNextPage($shipments, Fixture::pageSize());

            $firstIdOfFirstPage = $shipments['shipments'][0]->id;
            $secondIdOfSecondPage = $nextPage['shipments'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
    }

    /**
     * Test buying a Shipment.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('shipments/buy.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        $boughtShipment = self::$client->shipment->buy(
            $shipment->id,
            ['rate' => $shipment->lowestRate()]
        );

        $this->assertNotNull($boughtShipment->postage_label);
    }

    /**
     * Test buying a Shipment with a Rate object.
     */
    public function testBuyRateObject()
    {
        TestUtil::setupCassette('shipments/buyRateObject.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        $boughtShipment = self::$client->shipment->buy($shipment->id, $shipment->lowestRate());

        $this->assertNotNull($boughtShipment->postage_label);
    }

    /**
     * Test regenerating rates for a shipment.
     */
    public function testRegenerateRates()
    {
        TestUtil::setupCassette('shipments/regenerateRates.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $rates = self::$client->shipment->regenerateRates($shipment->id);

        $ratesArray = $rates['rates'];

        $this->assertIsArray($ratesArray);
        foreach ($ratesArray as $rate) {
            $this->assertInstanceOf(Rate::class, $rate);
        }
    }

    /**
     * Test converting the label format of a Shipment.
     */
    public function testConvertLabel()
    {
        TestUtil::setupCassette('shipments/convertLabel.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $shipmentWithLabel = self::$client->shipment->label(
            $shipment->id,
            ['file_format' => 'ZPL']
        );

        $this->assertNotNull($shipmentWithLabel->postage_label->label_zpl_url);
    }

    /**
     * Test converting the label format of a Shipment when we don't wrap the format.
     */
    public function testConvertLabelUnwrappedParam()
    {
        TestUtil::setupCassette('shipments/convertLabelUnwrappedParam.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $shipmentWithLabel = self::$client->shipment->label($shipment->id, 'ZPL');

        $this->assertNotNull($shipmentWithLabel->postage_label->label_zpl_url);
    }

    /**
     * Test insuring a Shipment.
     *
     * If the shipment was purchased with a USPS rate, it must have had its insurance set to `0` when bought
     * so that USPS doesn't automatically insure it so we could manually insure it here.
     */
    public function testInsure()
    {
        TestUtil::setupCassette('shipments/insure.yml');

        $shipmentData = Fixture::oneCallBuyShipment();
        // Set to 0 so USPS doesn't insure this automatically and we can insure the shipment manually
        $shipmentData['insurance'] = 0;

        $shipment = self::$client->shipment->create($shipmentData);

        $insuredShipment = self::$client->shipment->insure(
            $shipment->id,
            ['amount' => '100']
        );

        $this->assertEquals('100.00', $insuredShipment->insurance);
    }

    /**
     * Test insuring a Shipment when we don't wrap the params.
     *
     * If the shipment was purchased with a USPS rate, it must have had its insurance set to `0` when bought
     * so that USPS doesn't automatically insure it so we could manually insure it here.
     */
    public function testInsureUnwrappedParam()
    {
        TestUtil::setupCassette('shipments/insureUnwrappedParam.yml');

        $shipmentData = Fixture::oneCallBuyShipment();
        // Set to 0 so USPS doesn't insure this automatically and we can insure the shipment manually
        $shipmentData['insurance'] = 0;

        $shipment = self::$client->shipment->create($shipmentData);

        $insuredShipment = self::$client->shipment->insure($shipment->id, '100');

        $this->assertEquals('100.00', $insuredShipment->insurance);
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
        TestUtil::setupCassette('shipments/refund.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $refundedShipment = self::$client->shipment->refund($shipment->id);

        $this->assertEquals('submitted', $refundedShipment->refund_status);
    }

    /**
     * Test retrieving SmartRates for a shipment.
     */
    public function testSmartRate()
    {
        TestUtil::setupCassette('shipments/smartrates.yml');

        $shipment = self::$client->shipment->create(Fixture::basicShipment());

        $this->assertNotNull($shipment->rates);

        $smartRates = self::$client->shipment->getSmartRates($shipment->id);
        $this->assertEquals($shipment->rates[0]['id'], $smartRates[0]['id']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_50']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_75']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_85']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_90']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_95']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_97']);
        $this->assertNotNull($smartRates[0]['time_in_transit']['percentile_99']);
    }

    /**
     * Test creating a Shipment with empty or null objects and arrays.
     */
    public function testCreateEmptyObjects()
    {
        TestUtil::setupCassette('shipments/createEmptyObjects.yml');

        $shipmentData = Fixture::basicShipment();
        $shipmentData['customs_info']['customs_items'] = [];
        $shipmentData['options'] = null;
        $shipmentData['tax_identifiers'] = null;
        $shipmentData['reference'] = '';

        $shipment = self::$client->shipment->create($shipmentData);

        $this->assertInstanceOf(Shipment::class, $shipment);
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
        TestUtil::setupCassette('shipments/createTaxIdentifiers.yml');

        $shipmentData = Fixture::basicShipment();
        $shipmentData['tax_identifiers'] = [Fixture::taxIdentifier()];

        $shipment = self::$client->shipment->create($shipmentData);

        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertStringMatchesFormat('shp_%s', $shipment->id);
        $this->assertEquals('IOSS', $shipment->tax_identifiers[0]['tax_id_type']);
    }

    /**
     * Test creating a Shipment when only IDs are used.
     */
    public function testCreateWithIds()
    {
        TestUtil::setupCassette('shipments/createWithIds.yml');

        $fromAddress = self::$client->address->create(Fixture::caAddress1());
        $toAddress = self::$client->address->create(Fixture::caAddress1());
        $parcel = self::$client->parcel->create(Fixture::basicParcel());

        $shipment = self::$client->shipment->create([
            'from_address' => ['id' => $fromAddress->id],
            'to_address' => ['id' => $toAddress->id],
            'parcel' => ['id' => $parcel->id],
        ]);

        $this->assertInstanceOf(Shipment::class, $shipment);
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
        TestUtil::setupCassette('shipments/lowestRate.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        // Test lowestRate with no filters
        $lowestRate = $shipment->lowestRate();
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.82', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowestRate with service filter (this rate is higher than the lowest but should filter)
        $lowestRate = $shipment->lowestRate([], ['Priority']);
        $this->assertEquals('Priority', $lowestRate['service']);
        $this->assertEquals('8.15', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowestRate with carrier filter (should error due to bad carrier)
        try {
            $lowestRate = $shipment->lowestRate(['BAD CARRIER'], []);
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }

    /**
     * Test various usage alterations of the lowestRate method when excluding params by appending `!` to the string.
     */
    public function testLowestRateExclusions()
    {
        TestUtil::setupCassette('shipments/lowestRateExclusions.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        // Test lowest rate by excluding a carrier (this is a weak test but we cannot assume existence
        // of a non-USPS carrier).
        $lowestRate = $shipment->lowestRate(['!RandomCarrier']);
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.82', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);

        // Test lowest rate by excluding the `Priority` service
        $lowestRate = $shipment->lowestRate([], ['!Priority']);
        $this->assertEquals('First', $lowestRate['service']);
        $this->assertEquals('5.82', $lowestRate['rate']);
        $this->assertEquals('USPS', $lowestRate['carrier']);
    }

    /**
     * Test various usage alterations of the `lowestSmartRate` and getLowestSmartRate` methods.
     *
     * These tests are unfortunately combined because the VCR can't pull cassettes correctly
     * when testing these two functions in different tests/cassettes.
     */
    public function testLowestSmartRateVariations()
    {
        TestUtil::setupCassette('shipments/lowestSmartRateVariations.yml');

        $shipment = self::$client->shipment->create(Fixture::fullShipment());

        // Test lowestSmartRate with no filters
        $lowestSmartRate = self::$client->shipment->lowestSmartRate($shipment->id, 3, 'percentile_85');
        $this->assertEquals('First', $lowestSmartRate['service']);
        $this->assertEquals(5.82, $lowestSmartRate['rate']);
        $this->assertEquals('USPS', $lowestSmartRate['carrier']);

        // Test lowestSmartRate with invalid filters (should error due to strict delivery_days)
        try {
            self::$client->shipment->lowestSmartRate($shipment->id, 0, 'percentile_85');
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowestSmartRate with invalid filters (should error due to invalid delivery_accuracy)
        try {
            self::$client->shipment->lowestSmartRate($shipment->id, 3, 'BAD_ACCURACY');
        } catch (InvalidParameterException $error) {
            $this->assertEquals(
                'Invalid delivery_accuracy value, must be one of: ["percentile_50","percentile_75","percentile_85","percentile_90","percentile_95","percentile_97","percentile_99"]', // phpcs:ignore
                $error->getMessage()
            );
        }

        $shipment = self::$client->shipment->create(Fixture::basicShipment());
        $smartRates = self::$client->shipment->getSmartRates($shipment->id);

        // Test lowestSmartRate with valid filters
        $lowestSmartRate = Util::getLowestSmartRate($smartRates, 3, 'percentile_85');
        $this->assertEquals('First', $lowestSmartRate['service']);
        $this->assertEquals(5.82, $lowestSmartRate['rate']);
        $this->assertEquals('USPS', $lowestSmartRate['carrier']);

        // Test lowestSmartRate with invalid filters (should error due to strict delivery_days)
        try {
            Util::getLowestSmartRate($smartRates, 0, 'percentile_90');
        } catch (FilteringException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }

        // Test lowestSmartRate with invalid filters (should error due to invalid delivery_accuracy)
        try {
            Util::getLowestSmartRate($smartRates, 3, 'BAD_ACCURACY');
        } catch (InvalidParameterException $error) {
            $this->assertEquals(
                'Invalid delivery_accuracy value, must be one of: ["percentile_50","percentile_75","percentile_85","percentile_90","percentile_95","percentile_97","percentile_99"]', // phpcs:ignore
                $error->getMessage()
            );
        }
    }

    /**
     * Tests generating a form for a shipment.
     */
    public function testGenerateForm()
    {
        TestUtil::setupCassette('shipments/generateForm.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $formType = 'return_packing_slip';
        $shipmentWithForm = self::$client->shipment->generateForm(
            $shipment->id,
            $formType,
            Fixture::rmaFormOtions()
        );

        $this->assertEquals(1, count($shipmentWithForm->forms));

        $form = $shipmentWithForm->forms[0];

        $this->assertEquals($formType, $form->form_type);
        $this->assertNotNull($form->form_url);
    }

    /**
     * Tests creating a carbon offset shipment.
     */
    public function testCreateCarbonOffsetShipment()
    {
        TestUtil::setupCassette('shipments/createCarbonOffsetShipment.yml');

        $shipment = self::$client->shipment->create(Fixture::basicShipment(), true);

        $this->assertInstanceOf(Shipment::class, $shipment);

        foreach ($shipment->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }

    /**
     * Tests buying a carbon offset shipment.
     */
    public function testBuyCarbonOffsetShipment()
    {
        TestUtil::setupCassette('shipments/buyCarbonOffsetShipment.yml');

        $shipment = self::$client->shipment->create(Fixture::basicShipment());

        $boughtShipment = self::$client->shipment->buy(
            $shipment->id,
            ['rate' => $shipment->lowestRate()],
            true,
        );

        $this->assertInstanceOf(Shipment::class, $boughtShipment);

        $foundCarbonOffset = false;

        foreach ($boughtShipment->fees as $fee) {
            if ($fee->type == 'CarbonOffsetFee') {
                $foundCarbonOffset = true;
            }
        }

        $this->assertTrue($foundCarbonOffset);
    }

    /**
     * Tests one call buy a carbon offset shipment.
     */
    public function testOneCallBuyCarbonOffsetShipment()
    {
        TestUtil::setupCassette('shipments/oneCallBuyCarbonOffsetShipment.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment(), true);

        $this->assertInstanceOf(Shipment::class, $shipment);

        foreach ($shipment->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }

    /**
     * Tests rerate a shipment with carbon offset.
     */
    public function testRerateShipmentWithCarbonOffset()
    {
        TestUtil::setupCassette('shipments/rerateCarbonOffsetShipment.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $newCarbonOffset = self::$client->shipment->regenerateRates($shipment->id, null, true);
        foreach ($newCarbonOffset->rates as $rate) {
            $this->assertNotNull($rate->carbon_offset);
        }
    }

    /**
     * Tests buying a shipment with an end shipper.
     */
    public function testBuyShipmentWithEndShipper()
    {
        TestUtil::setupCassette('shipments/buyShipmentWithEndShipper.yml');

        $endShipper = self::$client->endShipper->create(Fixture::caAddress1());

        $shipment = self::$client->shipment->create(Fixture::basicShipment());

        $lowestRate = $shipment->lowestRate();

        $boughtShipment = self::$client->shipment->buy(
            $shipment->id,
            ['rate' => $lowestRate],
            false,
            $endShipper->id
        );

        $this->assertNotNull($boughtShipment->postage_label);
    }

    /**
     * Tests that we retrieve time-in-transit data for each of the Rates of a Shipment.
     */
    public function testRetrieveEstimatedDeliveryDate()
    {
        TestUtil::setupCassette('shipments/retrieveEstimatedDeliveryDate.yml');

        $shipment = self::$client->shipment->create(Fixture::basicShipment());

        $rates = self::$client->shipment->retrieveEstimatedDeliveryDate(
            $shipment->id,
            Fixture::plannedShipDate(),
        );

        foreach ($rates as $entry) {
            $this->assertNotNull($entry->easypost_time_in_transit_data);
        }
    }
}
