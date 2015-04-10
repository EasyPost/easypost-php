<?php
require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

define("ECHO_ALL_CA", true);
define("ECHO_CA_TYPES", false);
define("CREATE_NEW_CA", false);
define("EDIT_CA", false);
define("DELETE_CA", false);

// retrieve all of your CarrierAccounts
if (ECHO_ALL_CA) {
  $my_carrier_accounts = \EasyPost\CarrierAccount::all();
  print_r($my_carrier_accounts);
}

// This method returns all CarrierAccount types that are available
// to you to create, and the structure of their credentials.
// Especially useful for creating your own interface for collecting
// an end user's CarrierAccount credentials.
if (ECHO_CA_TYPES) {
  $carrier_account_types = \EasyPost\CarrierAccount::types();
  foreach($carrier_account_types as $ca_type) {
    print("===========\n");
    print("Carrier Account type: " . $ca_type->type . "\n");
    print("Readable name: " . $ca_type->readable . "\n");
    print("Logo: " . $ca_type->logo . "\n");
    print("Credentials: \n");
    foreach($ca_type->fields["credentials"] as $key => $value) {
      print("  " . $key . " - ");
      print("  " . $value->label . " (" . $value->visibility . ")\n");
    }
    print("===========\n\n");
  }
}

// create a new CarrierAccount
if (CREATE_NEW_CA) {
  try {
    $new_ups_account = \EasyPost\CarrierAccount::create(array(
      'type' => "UpsAccount",
      'description' => "My third UPS account.",
      'reference' => "ups02",
      'credentials' => array(
        'account_number' => "A1A1A1",
        'user_id' => "UPSDOTCOM_USERNAME",
        'password' => "UPSDOTCOM_PASSWORD",
        'access_license_number' => "UPS_ACCESS_LICENSE_NUMBER"
      )
    ));
    print_r($new_ups_account);
  } catch (Exception $e) {
    $e->prettyPrint();
  }
}

// edit an existing CarrierAccount
if (EDIT_CA) {
  $ups_account = \EasyPost\CarrierAccount::retrieve("ups02");
  $ups_account->credentials->account_number = "B2B2B2";
  $ups_account->description = "My second UPS account.";
  $ups_account->save();

  print_r($ups_account);
}

// delete a CarrierAccount
if (DELETE_CA) {
  $ups_account = \EasyPost\CarrierAccount::retrieve("ups02");
  $ups_account->delete();

  print_r($ups_account);
}

