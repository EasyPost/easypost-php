<?php

namespace EasyPost\Test;

class Fixture
{
    // Read fixture data from the fixtures JSON file
    private static function readFixtureData(): mixed
    {
        $currentDir = getcwd();
        $data = file_get_contents("$currentDir/examples/official/fixtures/client-library-fixtures.json");

        return json_decode($data, true);
    }

    // We keep the page_size of retrieving `all` records small so cassettes stay small
    public static function pageSize(): int
    {
        return self::readFixtureData()['page_sizes']['five_results'];
    }

    // This is the USPS carrier account ID that comes with your EasyPost account by
    // default and should be used for all tests.
    public static function uspsCarrierAccountId(): string
    {
        // Fallback to the EasyPost PHP Client Library Test User USPS carrier account ID due to strict matching
        $uspsCarrierAccountId = getenv('USPS_CARRIER_ACCOUNT_ID') !== false
            ? getenv('USPS_CARRIER_ACCOUNT_ID') : 'ca_8dc116debcdb49b5a66a2ddee4612600';

        return $uspsCarrierAccountId;
    }

    public static function usps(): string
    {
        return self::readFixtureData()['carrier_strings']['usps'];
    }

    public static function uspsService(): string
    {
        return self::readFixtureData()['service_names']['usps']['first_service'];
    }

    public static function pickupService(): string
    {
        return self::readFixtureData()['service_names']['usps']['pickup_service'];
    }

    public static function reportType(): string
    {
        return self::readFixtureData()['report_types']['shipment'];
    }

    public static function reportDate(): string
    {
        return '2022-04-09';
    }

    public static function webhookUrl(): string
    {
        return self::readFixtureData()['webhook_url'];
    }

    /**
     * @return array<mixed>
     */
    public static function caAddress1(): array
    {
        return self::readFixtureData()['addresses']['ca_address_1'];
    }

    /**
     * @return array<mixed>
     */
    public static function caAddress2(): array
    {
        return self::readFixtureData()['addresses']['ca_address_2'];
    }

    /**
     * @return array<mixed>
     */
    public static function incorrectAddress(): array
    {
        return self::readFixtureData()['addresses']['incorrect'];
    }

    /**
     * @return array<mixed>
     */
    public static function basicParcel(): array
    {
        return self::readFixtureData()['parcels']['basic'];
    }

    /**
     * @return array<mixed>
     */
    public static function basicCustomsItem(): array
    {
        return self::readFixtureData()['customs_items']['basic'];
    }

    /**
     * @return array<mixed>
     */
    public static function basicCustomsInfo(): array
    {
        return self::readFixtureData()['customs_infos']['basic'];
    }

    /**
     * @return array<mixed>
     */
    public static function taxIdentifier(): array
    {
        return self::readFixtureData()['tax_identifiers']['basic'];
    }

    /**
     * @return array<mixed>
     */
    public static function basicShipment(): array
    {
        return self::readFixtureData()['shipments']['basic_domestic'];
    }

    /**
     * @return array<mixed>
     */
    public static function fullShipment(): array
    {
        return self::readFixtureData()['shipments']['full'];
    }

    /**
     * @return array<mixed>
     */
    public static function oneCallBuyShipment(): array
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

    /**
     * This fixture will require you to add a `shipment` key with a Shipment object from a test.
     * If you need to re-record cassettes, increment the date below and ensure it is one day in the future,
     * USPS only does "next-day" pickups including Saturday but not Sunday or Holidays.
     *
     * @return array<mixed>
     */
    public static function basicPickup(): array
    {
        $pickupDate = '2023-11-24';

        $pickupData = self::readFixtureData()['pickups']['basic'];
        $pickupData['min_datetime'] = $pickupDate;
        $pickupData['max_datetime'] = $pickupDate;

        return $pickupData;
    }

    /**
     * @return array<mixed>
     */
    public static function basicCarrierAccount(): array
    {
        return self::readFixtureData()['carrier_accounts']['basic'];
    }


    /**
     * This fixture will require you to add a `tracking_code` key with a tracking code from a shipment
     *
     * @return array<mixed>
     */
    public static function basicInsurance(): array
    {
        return self::readFixtureData()['insurances']['basic'];
    }

    /**
     * @return array<mixed>
     */
    public static function basicOrder(): array
    {
        return self::readFixtureData()['orders']['basic'];
    }

    public static function eventJson(): mixed
    {
        $currentDir = getcwd();
        $data = file_get_contents("$currentDir/examples/official/fixtures/event-body.json");

        return json_encode(json_decode($data, true));
    }

    public static function eventBytes(): mixed
    {
        $currentDir = getcwd();
        $eventBytesFilepath = file("$currentDir/examples/official/fixtures/event-body.json");
        $data = $eventBytesFilepath[0];

        return mb_convert_encoding(json_encode(json_decode($data, true)), 'UTF-8', mb_list_encodings());
    }

    /**
     * The credit card details below are for a valid proxy card usable
     * for tests only and cannot be used for real transactions.
     * DO NOT alter these details with real credit card information.
     *
     * @return array<mixed>
     */
    public static function creditCardDetails(): array
    {
        return self::readFixtureData()['credit_cards']['test'];
    }

    /**
     * @return array<mixed>
     */
    public static function rmaFormOtions(): array
    {
        return self::readFixtureData()['form_options']['rma'];
    }

    public static function plannedShipDate(): string
    {
        return '2023-11-24';
    }
}
