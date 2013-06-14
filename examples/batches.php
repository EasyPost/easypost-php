<?php

require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// create addresses
$sf_address_params = array(
    "name"    => "Jon Calhoun",
    "street1" => "388 Townsend St",
    "street2" => "Apt 20",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94107-8273",
    "phone"   => "415-456-7890"
);
$sf2_address_params = array(
    "name"    => "Dirk Diggler",
    "street1" => "63 Ellis Street",
    "street2" => "Suite 1290",
    "city"    => "San Francisco",
    "state"   => "CA",
    "zip"     => "94102",
    "phone"   => "415-482-2937"
);
$canada_address_params = array(
    "name"    => "Sawyer Bateman",
    "street1" => "1A Larkspur Cres",
    "city"    => "St. Albert",
    "state"   => "AB",
    "zip"     => "t8n2m4",
    "country" => "CA",
    "phone"   => "780-252-8464"
);

// create parcel
$parcel_params_1 = array(
    // "length" => 20.2,
    // "width" => 10.9,
    // "height" => 5,
    "predefined_package" => 'Parcel',
    "weight" => 222.9
);
$parcel_params_2 = array(
    "length" => 20.2,
    "width" => 10.9,
    "height" => 5,
    // "predefined_package" => 'Parcel',
    "weight" => 22.9
);

// customs info form
$customs_info_params = array(
    "customs_certify"      => true,
    "customs_signer"       => "Borat Sagdiyev",
    "contents_type"        => "gift",
    "contents_explanation" => "", // only necessary for contents_type=other
    "eel_pfc"              => "NOEEI 30.36",
    "non_delivery_option"  => "abandon",
    "restriction_type"     => "none",
    "restriction_comments" => "",
    "customs_items"        => array(
        array(
            "description"      => "Many, many EasyPost stickers.",
            "hs_tariff_number" => 123456,
            "origin_country"   => "US",
            "quantity"         => 1,
            "value"            => 879.47,
            "weight"           => 14
        )
    )
);

// get a list of all my batches
// $all = \EasyPost\Batch::all();
// print_r($all);

// retrieve a batch
// $retrieved_batch = \EasyPost\Batch::retrieve('batch_jlELe1ki');
// if ($retrieved_batch->status->created == 0 && empty($retrieved_batch->label_url)) {
//     // retrieve shipment details if the batch processing is complete
//     for($i = 0, $j = count($retrieved_batch->shipments); $i < $j; $i++) {
//         $retrieved_batch->shipments[$i]->refresh();
//     }
//     $retrieved_batch->label(array('file_format' => 'epl2'));
// }
// print_r($retrieved_batch);

// create shipment batch
$new_batch = \EasyPost\Batch::create_and_buy(array('shipment' => array(
    array(
        'from_address' => $sf_address_params,
        'to_address'   => $sf2_address_params,
        'parcel'       => $parcel_params_1,
        'carrier'      => 'USPS',
        'service'      => 'Priority',
        'reference'    => 'order_12345'
    ),
    array(
        'from_address' => $sf_address_params,
        'to_address'   => $canada_address_params,
        'parcel'       => $parcel_params_2,
        'customs_info' => $customs_info_params,
        'carrier'      => 'USPS',
        'service'      => 'FirstClassPackageInternationalService',
        'reference'    => 'order_67890'
    ),
)));
print_r($new_batch);
