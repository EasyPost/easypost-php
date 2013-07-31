<?php

// require_once("../vendor/autoload.php");
require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "9499907123456123456781";

$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code));

print_r($tracker);
