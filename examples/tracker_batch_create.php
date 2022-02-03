<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey(getenv('API_KEY'));

$trackers_rep = [];

for ($i = 1; $i < 6; $i++) {
    $tracker = [];
    $tracker["tracking_code"] = "EZ200000000$i";
    $tracker["carrier"] = "USPS";
    array_push($trackers_rep, $tracker);
}

$trackers = \EasyPost\Tracker::create_list($trackers_rep);

// The create_list endpoint does not return a response. You'll want to capture these
// tracker creation events via webhook or create trackers individually to get their response.
