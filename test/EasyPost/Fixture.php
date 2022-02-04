<?php

namespace EasyPost\Test;

class Fixture
{
    public static function page_size()
    {
        return 5;
    }

    // Use any rate ID from any of the shipment cassettes
    public static function rate_id()
    {
        return 'rate_8d33b878a45f42d48b796dd0d6c434c1';
    }

    // If ever these need to change due to re-recording cassettes, simply increment both dates by 1
    public static function report_start_date()
    {
        return '2022-02-01';
    }

    public static function report_end_date()
    {
        return '2022-02-03';
    }

    public static function basic_address()
    {
        return [
            "street1" => "388 Townsend St",
            "street2" => "Apt 20",
            "city"    => "San Francisco",
            "state"   => "CA",
            "zip"     => "94107",
        ];
    }

    public static function incorrect_address_to_verify()
    {
        return [
            "verify"  => [true],
            "street1" => "417 montgomery streat",
            "street2" => "FL 5",
            "city"    => "San Francisco",
            "state"   => "CA",
            "zip"     => "94104",
            "country" => "US",
            "company" => "EasyPost",
            "phone"   => "415-123-4567",
        ];
    }

    public static function basic_parcel()
    {
        return [
            'length'    => '10',
            'width'     => '8',
            'height'    => '4',
            'weight'    => '15',
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
}
