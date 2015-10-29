<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "EZ2000000002";
$carrier = "USPS";

// create test tracker
$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));

var_dump($tracker->id);                      // This is random

// retrieve tracker by id
$tracker2 = \EasyPost\Tracker::retrieve($tracker->id);

var_dump($tracker2->id);

$params = array();                          // Should be the same as above


// retrieve all trackers by tracking_code
$trackers = \EasyPost\Tracker::all(array('tracking_code' => $tracking_code));

var_dump(count($trackers["trackers"]));      // Should be 30
var_dump($trackers["has_more"]);             // Should be true, unless the count() isn't 30
var_dump($trackers["trackers"][0]->id);      // Should be the same as the ids above

// create another test tracker
$tracker3 = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => $carrier));

var_dump($tracker3->id);                     // This is random

// retrieve all created since 'tracker'
$trackers2 = \EasyPost\Tracker::all(array('after_id' => $tracker->id));

var_dump(count($trackers2["trackers"]));     // Should be 1
var_dump($trackers2["has_more"]);            // Should be false
var_dump($trackers2["trackers"][0]->id);     // Should be the same as the id for tracker3
