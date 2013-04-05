<?php

require_once("../lib/easypost.php");

EasyPost::setApiKey('nri9zrYAnHjSbPUJ7YbhVQ');

// all
$all = EasyPost_ScanForm::all();
echo "<h4>EasyPost_ScanForm::all():</h4>";

echo "<p>";
$all_count = count($all);
for($i = 0; $i < $all_count; $i++) {
  print_r(EasyPost_ScanForm::retrieve($all[$i]->id));
  echo "<br><br>";
}
echo "</p>";

// retrieve
$retrieve = EasyPost_ScanForm::retrieve("5");
echo '<h4>EasyPost_ScanForm::retrieve("5"):</h4>';
echo '<p>';
print_r($retrieve);
echo '</p>';

// create
$address_param = array("name" => "Jon Calhoun",
                      "street1" => "388 Townsend St",
                      "street2" => "Apt 20",
                      "city" => "San Francisco",
                      "state" => "CA",
                      "zip" => "94107");
$tracking_codes = "123456,123455,123454,123453,123452";
$created = EasyPost_ScanForm::create(array("address" => $address_param, "tracking_codes" => $tracking_codes));
echo '<h4>EasyPost_ScanForm::create(array("address" => $address_param, "tracking_codes" => $tracking_codes)):</h4>';
echo '<p>';
print_r($created);
echo '</p>';
