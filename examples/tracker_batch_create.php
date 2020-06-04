<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$trackers_rep = array();

for ($i = 1; $i < 6; $i++) {
    $tracker = array();
    $tracker["tracking_code"] = "EZ200000000$i";
    $tracker["carrier"] = "USPS";
    array_push($trackers_rep, $tracker);
}

$trackers = \EasyPost\Tracker::create_list($trackers_rep);

// The create_list endpoint does not return a response. You'll want to capture these
// tracker creation events via webhook or create trackers individually to get their response.
