<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');


$trackers_rep = array();

for ($i = 0; $i < 5; $i++) {
  $tracker = array();
  $tracker["tracking_code"] = "EZ2000000002";
  $tracker["carrier"] = "USPS";
  array_push($trackers_rep, $tracker);
}

$trackers = \EasyPost\Tracker::create_list($trackers_rep);

var_dump($trackers);
