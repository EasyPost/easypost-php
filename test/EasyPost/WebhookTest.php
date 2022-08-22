<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Test\Fixture;
use EasyPost\Webhook;
use VCR\VCR;

class WebhookTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a Webhook.
     */
    public function testCreate()
    {
        VCR::insertCassette('webhooks/create.yml');

        $webhook = Webhook::create([
            'url' => Fixture::webhookUrl(),
        ]);

        $this->assertInstanceOf('\EasyPost\Webhook', $webhook);
        $this->assertStringMatchesFormat('hook_%s', $webhook->id);
        $this->assertEquals(Fixture::webhookUrl(), $webhook->url);

        $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test retrieving a Webhook.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('webhooks/retrieve.yml');

        $webhook = Webhook::create([
            'url' => Fixture::webhookUrl(),
        ]);

        $retrievedWebhook = Webhook::retrieve($webhook->id);

        $this->assertInstanceOf('\EasyPost\Webhook', $retrievedWebhook);
        $this->assertEquals($webhook, $retrievedWebhook);

        $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test retrieving all webhooks.
     */
    public function testAll()
    {
        VCR::insertCassette('webhooks/all.yml');

        $webhooks = Webhook::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $webhookArray = $webhooks['webhooks'];

        $this->assertLessThanOrEqual($webhookArray, Fixture::pageSize());
        $this->assertContainsOnlyInstancesOf('\EasyPost\Webhook', $webhookArray);
    }

    /**
     * Test updating a Webhook.
     */
    public function testUpdate()
    {
        VCR::insertCassette('webhooks/update.yml');

        $webhook = Webhook::create([
            'url' => Fixture::webhookUrl(),
        ]);

        $response = $webhook->update();

        // This endpoint/method does not return anything useful, just make sure the request doesn't fail
        $this->assertInstanceOf('\EasyPost\Webhook', $response);

        $response = $webhook->delete(); // We are deleting the webhook here so we don't keep sending events to a dead webhook.
    }

    /**
     * Test deleting a Webhook.
     */
    public function testDelete()
    {
        VCR::insertCassette('webhooks/delete.yml');

        $webhook = Webhook::create([
            'url' => Fixture::webhookUrl(),
        ]);

        $response = $webhook->delete();

        // This endpoint/method does not return anything, just make sure the request doesn't fail
        $this->assertInstanceOf('\EasyPost\Webhook', $response);
    }

    /**
     * Test a webhook signature that is originated from EasyPost by comparing the HMAC header
     * to a shared secret.
     */
    public function testValidateWebhook()
    {
        $webhookSecret = 'sécret';
        $expectedHmacSignature = 'hmac-sha256-hex=e93977c8ccb20363d51a62b3fe1fc402b7829be1152da9e88cf9e8d07115a46b';
        $headers = [
            'X-Hmac-Signature' => $expectedHmacSignature
        ];

        $webhookBody = Webhook::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);

        $this->assertEquals('batch.created', $webhookBody->description);
    }

    /**
     * Test a webhook signature that has invalid secret.
     */
    public function testValidateWebhookInvalidSecret()
    {
        $webhookSecret = 'invalid_secret';
        $headers = [
            'X-Hmac-Signature' => 'some-signature'
        ];

        try {
            Webhook::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);
        } catch (Error $error) {
            $this->assertEquals('Webhook received did not originate from EasyPost or had a webhook secret mismatch.', $error->getMessage());
        }
    }

    /**
     * Test a webhook signature does not have HMAC signature header.
     */
    public function testValidateWebhookMissingSecret()
    {
        $webhookSecret = '123';
        $headers = [
            'some-header' => 'some-signature'
        ];

        try {
            Webhook::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);
        } catch (Error $error) {
            $this->assertEquals('Webhook received does not contain an HMAC signature.', $error->getMessage());
        }
    }
}
