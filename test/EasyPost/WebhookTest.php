<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Webhook;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class WebhookTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a Webhook
     *
     * @return Webhook
     */
    public function testCreate()
    {
        VCR::insertCassette('webhooks/create.yml');

        $webhook = Webhook::create([
            "url" => "http://example.com"
        ]);

        $this->assertInstanceOf('\EasyPost\Webhook', $webhook);
        $this->assertStringMatchesFormat('hook_%s', $webhook->id);
        $this->assertEquals($webhook->url, 'http://example.com');

        // Return so the `retrieve` test can reuse this object
        return $webhook;
    }

    /**
     * Test retrieving a Webhook
     *
     * @param Webhook $webhook
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Webhook $webhook)
    {
        VCR::insertCassette('webhooks/retrieve.yml');

        $retrieved_webhook = Webhook::retrieve($webhook->id);

        $this->assertInstanceOf('\EasyPost\Webhook', $retrieved_webhook);
        $this->assertEquals($retrieved_webhook, $webhook);

        // Return so the `delete` test can reuse this object
        return $webhook;
    }

    /**
     * Test retrieving all webhooks
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
        foreach ($webhook_array as $webhook) {
            $this->assertInstanceOf('\EasyPost\Webhook', $webhook);
        }
    }

    /**
     * Test updating a Webhook
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->markTestSkipped('Cannot be easily tested - requires a disabled webhook.');
    }

    /**
     * Test deleting a Webhook
     *
     * @param Webhook $webhook
     * @return void
     * @depends testRetrieve
     */
    public function testDelete(Webhook $webhook)
    {
        VCR::insertCassette('webhooks/delete.yml');

        $response = $webhook->delete();

        // This endpoint/method does not return anything, just make sure the request doesn't fail
        $this->assertInstanceOf('\EasyPost\Webhook', $response);
    }
}
