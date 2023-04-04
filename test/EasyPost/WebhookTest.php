<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\SignatureVerificationException;
use EasyPost\Util\Util;
use EasyPost\Webhook;

class WebhookTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
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
    public function testCreate()
    {
        TestUtil::setupCassette('webhooks/create.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertStringMatchesFormat('hook_%s', $webhook->id);
        $this->assertEquals(Fixture::webhookUrl(), $webhook->url);

        // We are deleting the webhook here so we don't keep sending events to a dead webhook.
        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test retrieving a Webhook.
     */
    public function testRetrieve()
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
    public function testAll()
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
    public function testUpdate()
    {
        TestUtil::setupCassette('webhooks/update.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        $updatedWebhook = self::$client->webhook->update($webhook->id);

        // The response here won't differ since we don't update any data, just check we get the object back
        $this->assertInstanceOf(Webhook::class, $updatedWebhook);

        // We are deleting the webhook here so we don't keep sending events to a dead webhook.
        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test deleting a Webhook.
     */
    public function testDelete()
    {
        TestUtil::setupCassette('webhooks/delete.yml');

        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        try {
            self::$client->webhook->delete($webhook->id);
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
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

        $webhookBody = Util::validateWebhook(Fixture::eventBytes(), $headers, $webhookSecret);

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
    public function testValidateWebhookMissingSecret()
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
