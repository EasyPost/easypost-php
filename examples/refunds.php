<?php

require_once("../vendor/autoload.php");
\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

try {
    // create refund
    $refund = \EasyPost\Refund::create([
        "carrier" => "USPS",
        "tracking_codes" => "CJ123456789US,LN123456789US"
    ]);
    print_r($refund);

    // all
    $all = \EasyPost\Refund::all();
    print_r($all);
} catch (Exception $e) {
    echo "Status: " . $e->getHttpStatus() . ":\n";
    echo $e->getMessage();
    if (!empty($e->param)) {
        echo "\nInvalid param: {$e->param}";
    }
    exit();
}
