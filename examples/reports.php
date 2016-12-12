<?php

require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');


// Create a shipment report
$shipment_report = \EasyPost\Report::create(array("type" => "shipment"));
var_dump($shipment_report->id);

// Retrieve a shipment report
$shipment_report_2 = \EasyPost\Report::retrieve($shipment_report->id);
var_dump($shipment_report_2->id);

// Index shipment reports
$shipment_reports = \EasyPost\Report::all(array("type" => "shipment", "page_size" => 4));
var_dump(count($shipment_reports["reports"]));
var_dump($shipment_reports["has_more"]);


// Create a tracker report
$tracker_report = \EasyPost\Report::create(array("type" => "tracker"));
var_dump($tracker_report->id);

// Retrieve a tracker report
$tracker_report_2 = \EasyPost\Report::retrieve($tracker_report->id);
var_dump($tracker_report_2->id);

// Index tracker reports
$tracker_reports = \EasyPost\Report::all(array("type" => "tracker", "page_size" => 4));
var_dump(count($tracker_reports["reports"]));
var_dump($tracker_reports["has_more"]);


// Create a payment log report
$payment_log_report = \EasyPost\Report::create(array("type" => "payment_log"));
var_dump($payment_log_report->id);

// Retrieve a payment log report
$payment_log_report_2 = \EasyPost\Report::retrieve($payment_log_report->id);
var_dump($payment_log_report_2->id);

// Index payment log reports
$payment_log_reports = \EasyPost\Report::all(array("type" => "payment_log", "page_size" => 4));
var_dump(count($payment_log_reports["reports"]));
var_dump($payment_log_reports["has_more"]);
