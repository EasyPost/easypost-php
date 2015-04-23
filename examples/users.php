<?php
require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('PROD_MODE_KEY');

define("ECHO_ME", true);
define("CREATE_CHILD", false);
define("GET_CHILD", false);
define("EDIT_ME", false);
define("EDIT_CHILD", false);

// Retrieve your user record
if (ECHO_ME) {
  $me = \EasyPost\User::retrieve_me();
  print_r($me);
}

// Create a new child account. Children are used to logically group transactions
// and settings (such as carrier accounts) where all payments flow through the
// parent account. They should not be used in situations where you intend for
// an end user to log into the child account.
if (CREATE_CHILD) {
  try {
    $new_child = \EasyPost\User::create();
    $child_keys = $new_child->api_keys();
    // SAVE CHILD_KEYS TO MAKE REQUESTS AS THE CHILD ACCOUNT LATER

    print_r($new_child);
  } catch (Exception $e) {
    $e->prettyPrint();
  }
}

if (GET_CHILD) {
  $me = \EasyPost\User::retrieve_me();

  $child = \EasyPost\User::retrieve($me->children[0]->id);
  $child_keys = $child->api_keys();

  print_r($child);
}

if (EDIT_ME) {
  $me = \EasyPost\User::retrieve_me();
  $me->name = "My New Name";
  $me->email = "newemail@example.com";
  $me->recharge_threshold = 5000;
  try {
    $me->save();
  } catch (Exception $e) {
    $e->prettyPrint();
  } 

  print_r($me);
}

if (EDIT_CHILD) {
  $me = \EasyPost\User::retrieve_me();

  $child = \EasyPost\User::retrieve($me->children[0]->id);
  $child_keys = $child->api_keys();

  // retrieve the child with its own api key
  $child = \EasyPost\User::retrieve_me($child_keys["production"]);
  $child->name = "Child New Name";
  $child->email = "childnewemail@example.com";
  try {
    $child->save();
  } catch (Exception $e) {
    $e->prettyPrint();
  } 

  print_r($child);
}

