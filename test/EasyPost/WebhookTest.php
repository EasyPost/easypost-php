<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\SignatureVerificationException;
use EasyPost\Util\Util;
use EasyPost\Webhook;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a Webhook.
     */
    public function testCreate(): void
    {
        TestUtil::setupCassette('webhooks/create.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
            'webhook_secret' => Fixture::webhookSecret(),
            'custom_headers' => Fixture::webhookCustomHeaders(),
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertStringMatchesFormat('hook_%s', $webhook->id);
        $this->assertEquals(Fixture::webhookUrl(), $webhook->url);
        $this->assertEquals('test', $webhook->custom_headers[0]->name); // @phpstan-ignore-line
        $this->assertEquals('header', $webhook->custom_headers[0]->value); // @phpstan-ignore-line

        // We are deleting the webhook here so we don't keep sending events to a dead webhook.
        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test retrieving a Webhook.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('webhooks/retrieve.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        $retrievedWebhook = self::$client->webhook->retrieve($webhook->id);

        $this->assertInstanceOf(Webhook::class, $retrievedWebhook);
        $this->assertEquals($webhook, $retrievedWebhook);

        // We are deleting the webhook here so we don't keep sending events to a dead webhook.
        self::$client->webhook->delete($retrievedWebhook->id);
    }

    /**
     * Test retrieving all webhooks.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('webhooks/all.yml');

        $webhooks = self::$client->webhook->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $webhookArray = $webhooks['webhooks'];

        $this->assertLessThanOrEqual($webhookArray, Fixture::pageSize());
        $this->assertContainsOnlyInstancesOf(Webhook::class, $webhookArray);
    }

    /**
     * Test updating a Webhook.
     */
    public function testUpdate(): void
    {
        TestUtil::setupCassette('webhooks/update.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        $updatedWebhook = self::$client->webhook->update(
            $webhook->id,
            [
                'webhook_secret' => Fixture::webhookSecret(),
                'custom_headers' => Fixture::webhookCustomHeaders(),
            ]
        );

        $this->assertInstanceOf(Webhook::class, $updatedWebhook);
        $this->assertEquals('test', $updatedWebhook->custom_headers[0]->name); // @phpstan-ignore-line
        $this->assertEquals('header', $updatedWebhook->custom_headers[0]->value); // @phpstan-ignore-line

        // We are deleting the webhook here so we don't keep sending events to a dead webhook.
        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test deleting a Webhook.
     */
    public function testDelete(): void
    {
        TestUtil::setupCassette('webhooks/delete.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        try {
            self::$client->webhook->delete($webhook->id);
            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test a webhook signature that is originated from EasyPost by comparing the HMAC header
     * to a shared secret.
     */
    public function testValidateWebhook(): void
    {
        $headers = [
            'X-Hmac-Signature' => Fixture::webhookHmacSignature()
        ];

        $webhookBody = Util::validateWebhook(Fixture::eventBytes(), $headers, Fixture::webhookSecret());

        $this->assertEquals('tracker.updated', $webhookBody->description);
        $this->assertEquals(614.4, $webhookBody->result->weight); // Ensure we convert floats properly
    }

    /**
     * Test a webhook signature that has invalid secret.
     */
    public function testValidateWebhookInvalidSecret(): void
    {
        $webhookSecret = 'invalid_secret';
        $headers = [
            'X-Hmac-Signature' => 'some-signature'
        ];

        try {
            Util::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);
        } catch (SignatureVerificationException $error) {
            $this->assertEquals(
                'Webhook received did not originate from EasyPost or had a webhook secret mismatch.',
                $error->getMessage()
            );
        }
    }

    /**
     * Test a webhook signature does not have HMAC signature header.
     */
    public function testValidateWebhookMissingSecret(): void
    {
        $webhookSecret = '123';
        $headers = [
            'some-header' => 'some-signature'
        ];

        try {
            Util::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);
        } catch (SignatureVerificationException $error) {
            $this->assertEquals('Webhook received does not contain an HMAC signature.', $error->getMessage());
        }
    }
}
