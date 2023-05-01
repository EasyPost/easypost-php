<?php

namespace EasyPost\Test;

class Fixture
{
    // Read fixture data from the fixtures JSON file
    private static function readFixtureData()
    {
        $currentDir = getcwd();
        $data = file_get_contents("$currentDir/examples/official/fixtures/client-library-fixtures.json");

        return json_decode($data, true);
    }

    // We keep the page_size of retrieving `all` records small so cassettes stay small
    public static function pageSize()
    {
        return self::readFixtureData()['page_sizes']['five_results'];
    }

    // This is the USPS carrier account ID that comes with your EasyPost account by
    // default and should be used for all tests.
    public static function uspsCarrierAccountId()
    {
        // Fallback to the EasyPost PHP Client Library Test User USPS carrier account ID due to strict matching
        $uspsCarrierAccountId = getenv('USPS_CARRIER_ACCOUNT_ID') !== false
            ? getenv('USPS_CARRIER_ACCOUNT_ID') : 'ca_8dc116debcdb49b5a66a2ddee4612600';

        return $uspsCarrierAccountId;
    }

    public static function usps()
    {
        return self::readFixtureData()['carrier_strings']['usps'];
    }

    public static function uspsService()
    {
        return self::readFixtureData()['service_names']['usps']['first_service'];
    }

    public static function pickupService()
    {
        return self::readFixtureData()['service_names']['usps']['pickup_service'];
    }

    public static function reportType()
    {
        return self::readFixtureData()['report_types']['shipment'];
    }

    public static function reportDate()
    {
        return '2022-04-09';
    }

    public static function webhookUrl()
    {
        return self::readFixtureData()['webhook_url'];
    }

    public static function caAddress1()
    {
        return self::readFixtureData()['addresses']['ca_address_1'];
    }

    public static function caAddress2()
    {
        return self::readFixtureData()['addresses']['ca_address_2'];
    }

    public static function incorrectAddress()
    {
        return self::readFixtureData()['addresses']['incorrect'];
    }

    public static function basicParcel()
    {
        return self::readFixtureData()['parcels']['basic'];
    }

    public static function basicCustomsItem()
    {
        return self::readFixtureData()['customs_items']['basic'];
    }

    public static function basicCustomsInfo()
    {
        return self::readFixtureData()['customs_infos']['basic'];
    }

    public static function taxIdentifier()
    {
        return self::readFixtureData()['tax_identifiers']['basic'];
    }

    public static function basicShipment()
    {
        return self::readFixtureData()['shipments']['basic_domestic'];
    }

    public static function fullShipment()
    {
        return self::readFixtureData()['shipments']['full'];
    }

    public static function oneCallBuyShipment()
    {
        return [
            'to_address' => self::caAddress1(),
            'from_address' => self::caAddress2(),
            'parcel' => self::basicParcel(),
            'service' => self::uspsService(),
            'carrier_accounts' => [self::uspsCarrierAccountId()],
            'carrier' => self::usps(),
        ];
    }

    // This fixture will require you to add a `shipment` key with a Shipment object from a test.
    // If you need to re-record cassettes, increment the date below and ensure it is one day in the future,
    // USPS only does "next-day" pickups including Saturday but not Sunday or Holidays.
    public static function basicPickup()
    {
        $pickupDate = '2022-12-09';

        $pickupData = self::readFixtureData()['pickups']['basic'];
        $pickupData['min_datetime'] = $pickupDate;
        $pickupData['max_datetime'] = $pickupDate;

        return $pickupData;
    }

    public static function basicCarrierAccount()
    {
        return self::readFixtureData()['carrier_accounts']['basic'];
    }

    // This fixture will require you to add a `tracking_code` key with a tracking code from a shipment
    public static function basicInsurance()
    {
        return self::readFixtureData()['insurances']['basic'];
    }

    public static function basicOrder()
    {
        return self::readFixtureData()['orders']['basic'];
    }

    public static function eventJson()
    {
        $currentDir = getcwd();
        $data = file_get_contents("$currentDir/examples/official/fixtures/event-body.json");

        return json_encode(json_decode($data, true));
    }

    public static function eventBytes()
    {
        $currentDir = getcwd();
        $eventBytesFilepath = file("$currentDir/examples/official/fixtures/event-body.json");
        $data = $eventBytesFilepath[0];

        return mb_convert_encoding(json_encode(json_decode($data, true)), 'UTF-8', mb_list_encodings());
    }

    // The credit card details below are for a valid proxy card usable
    // for tests only and cannot be used for real transactions.
    // DO NOT alter these details with real credit card information.
    public static function creditCardDetails()
    {
        return self::readFixtureData()['credit_cards']['test'];
    }

    public static function rmaFormOtions()
    {
        return self::readFixtureData()['form_options']['rma'];
    }

    public static function plannedShipDate()
    {
        return '2023-04-28';
    }
}
