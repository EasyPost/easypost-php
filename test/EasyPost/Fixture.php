<?php

namespace EasyPost\Test;

class Fixture
{
    public static function page_size()
    {
        return 5;
    }

    // This is the carrier account ID for the default USPS account that comes by default. All tests should use this carrier account.
    public static function usps_carrier_account_id()
    {
        return 'ca_8dc116debcdb49b5a66a2ddee4612600';
    }

    // Use any rate ID from any of the shipment cassettes
    public static function rate_id()
    {
        return 'rate_8d33b878a45f42d48b796dd0d6c434c1';
    }

    // If ever these need to change due to re-recording cassettes, simply increment this date by 1
    public static function report_start_date()
    {
        return '2022-02-01';
    }

    // If ever these need to change due to re-recording cassettes, simply increment this date by 1
    public static function report_end_date()
    {
        return '2022-02-03';
    }

    public static function basic_address()
    {
        return [
            'name'      => 'Jack Sparrow',
            'company'   => 'EasyPost',
            'street1'   => '388 Townsend St',
            'street2'   => 'Apt 20',
            'city'      => 'San Francisco',
            'state'     => 'CA',
            'zip'       => '94107',
            'phone'     => '5555555555',
        ];
    }

    public static function incorrect_address_to_verify()
    {
        return [
            'verify'  => [true],
            'street1' => '417 montgomery streat',
            'street2' => 'FL 5',
            'city'    => 'San Francisco',
            'state'   => 'CA',
            'zip'     => '94104',
            'country' => 'US',
            'company' => 'EasyPost',
            'phone'   => '415-123-4567',
        ];
    }

    public static function pickup_address()
    {
        return [
            'name' => 'Dr. Steve Brule',
            'street1' => '179 N Harbor Dr',
            'city' => 'Redondo Beach',
            'state' => 'CA',
            'zip' => '90277',
            'country' => 'US',
            'phone' => '3331114444',
        ];
    }

    public static function basic_parcel()
    {
        return [
            'length'    => '10',
            'width'     => '8',
            'height'    => '4',
            'weight'    => '15.4',
        ];
    }

    public static function basic_customs_item()
    {
        return [
            'description' => 'Sweet shirts',
            'quantity' => 2,
            'weight' => 11,
            'value' => 23,
            'hs_tariff_number' => '654321',
            'origin_country' => 'US',
        ];
    }

    public static function basic_customs_info()
    {
        return [
            'eel_pfc' => 'NOEEI 30.37(a)',
            'customs_certify' => true,
            'customs_signer' => 'Steve Brule',
            'contents_type' => 'merchandise',
            'contents_explanation' => '',
            'restriction_type' => 'none',
            'non_delivery_option' => 'return',
            'customs_items' => [
                self::basic_customs_item(),
            ]
        ];
    }

    public static function tax_identifier()
    {
        return [
            'entity' => 'SENDER',
            'tax_id_type' => 'IOSS',
            'tax_id' => '12345',
            'issuing_country' => 'GB',
        ];
    }

    public static function basic_shipment()
    {
        return [
            'to_address' => self::basic_address(),
            'from_address' => self::basic_address(),
            'parcel' => self::basic_parcel(),
        ];
    }

    public static function full_shipment()
    {
        return [
            'to_address' => self::basic_address(),
            'from_address' => self::basic_address(),
            'parcel' => self::basic_parcel(),
            'customs_info'  => self::basic_customs_info(),
            'options' => [
                'label_format' => 'PNG', // Must be PNG so we can convert to ZPL later
                'invoice_number' => 123 // Tests that we encode integers to strings where appropriate
            ],
            'reference' => 123, // Tests that we encode integers to strings where appropriate
        ];
    }

    public static function one_call_buy_shipment()
    {
        return [
            'to_address' => self::basic_address(),
            'from_address' => self::basic_address(),
            'parcel' => self::basic_parcel(),
            'service' => 'First',
            'carrier_accounts' => [self::usps_carrier_account_id()],
        ];
    }

    // This fixture will require you to add a `shipment` key with a Shipment object from a test.
    // If you need to re-record cassettes, simply iterate the dates below and ensure they're one day in the future,
    // USPS only does "next-day" pickups including Saturday but not Sunday or Holidays.
    public static function basic_pickup()
    {
        return [
            'address' => self::basic_address(),
            'min_datetime' => '2022-02-05',
            'max_datetime' => '2022-02-06',
            'instructions' => 'Pickup at front door',
        ];
    }

    public static function event()
    {
        return json_encode([
            'mode' => 'production',
            'description' => 'batch.created',
            'previous_attributes' => ['state' => 'purchasing'],
            'pending_urls' => ['example.com/easypost-webhook'],
            'completed_urls' => [],
            'created_at' => '2015-12-03T19:09:19Z',
            'updated_at' => '2015-12-03T19:09:19Z',
            'result' => [
                'id' => 'batch_...',
                'object' => 'Batch',
                'mode' => 'production',
                'state' => 'purchased',
                'num_shipments' => 1,
                'reference' => null,
                'created_at' => '2015-12-03T19:09:19Z',
                'updated_at' => '2015-12-03T19:09:19Z',
                'scan_form' => null,
                'shipments' => [
                    [
                        'batch_status' => 'postage_purchased',
                        'batch_message' => null,
                        'id' => 'shp_a5b1348307694736aaqqqq8fqda53f93'
                    ]
                ],
                'status' => [
                    'created' => 0,
                    'queued_for_purchase' => 0,
                    'creation_failed' => 0,
                    'postage_purchased' => 1,
                    'postage_purchase_failed' => 0
                ],
                'pickup' => null,
                'label_url' => null
            ],
            'id' => 'evt_...',
            'object' => 'Event'
        ]);
    }
}
