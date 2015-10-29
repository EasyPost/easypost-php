<?php

require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

try {
    // create addresses
    $from_address = array(
        "company" => "EasyPost",
        "street1" => "388 Townsend St",
        "city"    => "San Francisco",
        "state"   => "CA",
        "zip"     => "94107-8273",
        "phone"   => "415-456-7890"
    );
    $parcel = array(
        "predefined_package" => 'Parcel',
        "weight"             => 22.9
    );

    // in your application orders will likely
    // come from an external data source
    $orders = array(
        array(
            "address"  => array(
                "name"    => "Ronald",
                "street1" => "6258 Amesbury St",
                "city"    => "San Diego",
                "state"   => "CA",
                "zip"     => "92114"
            ),
            "reference"   => "123456786"
        ),
        array(
            "address"  => array(
                "name"    => "Hamburgler",
                "street1" => "8308 Fenway Rd",
                "city"    => "Bethesda",
                "state"   => "MD",
                "zip"     => "20817"
            ),
            "reference"   => "123456787"
        ),
        array(
            "address"  => array(
                "name"    => "Grimace",
                "street1" => "10 Wall St",
                "city"    => "Burlington",
                "state"   => "MA",
                "zip"     => "01803"
            ),
            "reference"   => "123456788"
        )
    );

    // create shipments
    $shipments = array();
    for($i = 0, $j = count($orders); $i < $j; $i++) {
        $shipment = \EasyPost\Shipment::create(array(
            "to_address"   => $orders[$i]["address"],
            "from_address" => $from_address,
            "parcel"       => $parcel,
            "reference"    => $orders[$i]["reference"],
        ));
        $shipment->buy(array("rate" => $shipment->lowest_rate('usps')));
        $shipment->insure(array('amount' => 100));
        $shipments[] = $shipment;
    }

    // create a batch to hold shipments and scan_form
    $batch = \EasyPost\Batch::create();

    $batch->add_shipments(array("shipments" => $shipments)); // this could alternatively take a list of shipment_ids rather than shipment objects

    // request a scan form
    $batch->create_scan_form();

    // wait for scan form to complete
    while(empty($batch->scan_form)) {
        sleep(5);
        $batch->refresh();
    }

    // print scan form url
    print_r($batch);
    // print_r($batch->scan_form->form_url);



    // retrieve
    $retrieved = \EasyPost\ScanForm::retrieve($batch->scan_form->id);
    // print_r($retrieved);

    // all
    // $all = \EasyPost\ScanForm::all();
    //print_r($all);

} catch (Exception $e) {
    echo "Status: " . $e->getHttpStatus() . ":\n";
    exit($e->getMessage());
}
