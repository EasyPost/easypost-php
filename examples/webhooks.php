<?php

require_once("../lib/easypost.php");
\EasyPost\EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

// Webhook: create
$create_params = array("url" => "http://example.com");
$webhook = \EasyPost\Webhook::create($create_params);
var_dump($webhook->url);
var_dump($webhook->id);

// Webhook: retrieve
$webhook2 = \EasyPost\Webhook::retrieve($webhook->id);
var_dump($webhook2->id);

// Webhook: update
$webhook3 = $webhook->update();
var_dump($webhook3->id);

// Webhook: index
$webhooks = \EasyPost\Webhook::all();
var_dump($webhooks["webhooks"][count($webhooks["webhooks"]) - 1]->id);

// Webhook: delete
$webhook->delete();
try {
    $webhook4 = \EasyPost\Webhook::retrieve($webhook->id); // This should error
    var_dump("NOT DELETED");                               // This line should not be reached
} catch (Exception $e) {
    var_dump($e->getHttpBody());                           // code: NOT_FOUND
}
