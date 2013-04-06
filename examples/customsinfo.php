<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('nri9zrYAnHjSbPUJ7YbhVQ');

// all
$all = EasyPost_CustomsItem::all();
echo "<h4>EasyPost_CustomsItem::all():</h4>";

echo "<p>";
$all_count = count($all);
for($i = 0; $i < $all_count; $i++) {
  print_r(EasyPost_CustomsItem::retrieve($all[$i]->id));
  echo "<br><br>";
}
echo "</p>";

// retrieve
$retrieved = EasyPost_CustomsItem::retrieve("1");
echo '<h4>EasyPost_CustomsItem::retrieve("1"):</h4>';
echo '<p>';
print_r($retrieved);
echo '</p>';


// all
$all = EasyPost_CustomsInfo::all();
echo "<h4>EasyPost_CustomsInfo::all():</h4>";

echo "<p>";
$all_count = count($all);
for($i = 0; $i < $all_count; $i++) {
  print_r(EasyPost_CustomsInfo::retrieve($all[$i]->id));
  echo "<br><br>";
}
echo "</p>";

// retrieve
$retrieved = EasyPost_CustomsInfo::retrieve("1");
echo '<h4>EasyPost_CustomsInfo::retrieve("1"):</h4>';
echo '<p>';
print_r($retrieved);
echo '</p>';



// create
/*
$from_address = array("name" => "Jon Calhoun",
                      "street1" => "388 Townsend St",
                      "street2" => "Apt 20",
                      "city" => "San Francisco",
                      "state" => "CA",
                      "zip" => "94107");
$tracking_codes = "123456,123455,123454,123453,123452";

$created = EasyPost_ScanForm::create(array("address" => $from_address, "tracking_codes" => $tracking_codes));
echo '<h4>EasyPost_ScanForm::create(array("address" => $from_address, "tracking_codes" => $tracking_codes)):</h4>';
echo '<p>';
print_r($created);
echo '</p>';
*/

/* create from address object

$from = EasyPost_Address::create($from_address);
$created = EasyPost_ScanForm::create(array("address" => $from, "tracking_codes" => $tracking_codes));

$created = EasyPost_ScanForm::create(array("address" => $from_address, "tracking_codes" => $tracking_codes));
echo '<h4>EasyPost_ScanForm::create(array("address" => $from_address, "tracking_codes" => $tracking_codes)):</h4>';
echo '<p>';
print_r($created);
echo '</p>';

*/