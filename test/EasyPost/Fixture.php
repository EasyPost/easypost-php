<?php

namespace EasyPost\Test;

class Fixture
{
    // We keep the page_size of retrieving `all` records small so cassettes stay small
    public static function page_size()
    {
        return 5;
    }

    // This is the USPS carrier account ID that comes with your EasyPost account by default and should be used for all tests
    public static function usps_carrier_account_id()
    {
        // Fallback to the EasyPost PHP Client Library Test User USPS carrier account ID
        $usps_carrier_account_id = getenv('USPS_CARRIER_ACCOUNT_ID') !== false ? getenv('USPS_CARRIER_ACCOUNT_ID') : 'ca_8dc116debcdb49b5a66a2ddee4612600';

        return $usps_carrier_account_id;
    }

    public static function usps()
    {
        return 'USPS';
    }

    public static function usps_service()
    {
        return 'First';
    }

    public static function pickup_service()
    {
        return 'NextDay';
    }

    public static function report_type()
    {
        return 'shipment';
    }

    // If you need to re-record cassettes, increment this date by 1
    public static function report_date()
    {
        return '2022-04-09';
    }

    public static function webhook_url()
    {
        return 'http://example.com';
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
            'street1' => '417 montgomery street',
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
                'invoice_number' => 123 // Tests that we encode integers to strings where appropriate (PHP lib feature)
            ],
            'reference' => 123, // Tests that we encode integers to strings where appropriate (PHP lib feature)
        ];
    }

    public static function one_call_buy_shipment()
    {
        return [
            'to_address' => self::basic_address(),
            'from_address' => self::basic_address(),
            'parcel' => self::basic_parcel(),
            'service' => self::usps_service(),
            'carrier_accounts' => [self::usps_carrier_account_id()],
            'carrier' => self::usps(),
        ];
    }

    // This fixture will require you to add a `shipment` key with a Shipment object from a test.
    // If you need to re-record cassettes, increment the date below and ensure it is one day in the future,
    // USPS only does "next-day" pickups including Saturday but not Sunday or Holidays.
    public static function basic_pickup()
    {
        $pickup_date = '2022-05-16';

        return [
            'address' => self::basic_address(),
            'min_datetime' => $pickup_date,
            'max_datetime' => $pickup_date,
            'instructions' => 'Pickup at front door',
        ];
    }

    public static function basic_carrier_account()
    {
        return [
            'type' => 'UpsAccount',
            'credentials' => [
                'account_number' => 'A1A1A1',
                'user_id' => 'USERID',
                'password' => 'PASSWORD',
                'access_license_number' => 'ALN',
            ],
        ];
    }

    // This fixture will require you to add a `tracking_code` key with a tracking code from a shipment
    public static function basic_insurance()
    {
        return [
            'from_address' => self::basic_address(),
            'to_address' => self::basic_address(),
            'carrier' => self::usps(),
            'amount' => '100',
        ];
    }

    public static function basic_order()
    {
        return [
            'from_address' => self::basic_address(),
            'to_address' => self::basic_address(),
            'shipments' => [self::basic_shipment()],
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
                        'id' => 'shp_123...'
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

    public static function end_shipper_address()
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
            'country'   => 'US',
            'email'     => 'test@example.com',
        ];
    }
}
