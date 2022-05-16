<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use EasyPost\Webhook;
use VCR\VCR;

class WebhookTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a Webhook.
     *
     * @return Webhook
     */
    public function testCreate()
    {
        VCR::insertCassette('webhooks/create.yml');

        $webhook = Webhook::create([
            "url" => Fixture::webhook_url(),
        ]);

        $this->assertInstanceOf('\EasyPost\Webhook', $webhook);
        $this->assertStringMatchesFormat('hook_%s', $webhook->id);
        $this->assertEquals(Fixture::webhook_url(), $webhook->url);

        $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test retrieving a Webhook.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('webhooks/retrieve.yml');

        $webhook = Webhook::create([
            "url" => Fixture::webhook_url(),
        ]);

        $retrieved_webhook = Webhook::retrieve($webhook->id);

        $this->assertInstanceOf('\EasyPost\Webhook', $retrieved_webhook);
        $this->assertEquals($webhook, $retrieved_webhook);

        $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test retrieving all webhooks.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('webhooks/all.yml');

        $webhooks = Webhook::all([
            'page_size' => Fixture::page_size(),
        ]);

        $webhook_array = $webhooks['webhooks'];

        $this->assertLessThanOrEqual($webhook_array, Fixture::page_size());
        $this->assertContainsOnlyInstancesOf('\EasyPost\Webhook', $webhook_array);
    }

    /**
     * Test updating a Webhook.
     *
     * @return void
     */
    public function testUpdate()
    {
        VCR::insertCassette('webhooks/update.yml');

        $webhook = Webhook::create([
            "url" => Fixture::webhook_url(),
        ]);

        $response = $webhook->update();

        // This endpoint/method does not return anything useful, just make sure the request doesn't fail
        $this->assertInstanceOf('\EasyPost\Webhook', $response);

        $response = $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test deleting a Webhook.
     *
     * @return void
     */
    public function testDelete()
    {
        VCR::insertCassette('webhooks/delete.yml');

        $webhook = Webhook::create([
            "url" => Fixture::webhook_url(),
        ]);

        $response = $webhook->delete();

        // This endpoint/method does not return anything, just make sure the request doesn't fail
        $this->assertInstanceOf('\EasyPost\Webhook', $response);
    }
}
