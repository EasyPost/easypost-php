<?php

require_once("../lib/easypost.php");

\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

$tracking_code = "9611913638261560007008";

$tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code));
// $tracker = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code, 'carrier' => 'fedexsmartpost'));
// $tracker = \EasyPost\Tracker::retrieve('trk_xxxxxxxx');

print_r($tracker);
