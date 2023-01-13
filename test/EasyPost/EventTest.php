<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Event;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Payload;
use EasyPost\Util\Util;

class EventTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving all events.
     */
    public function testAll()
    {
        TestUtil::setupCassette('events/all.yml');

        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $eventsArray = $events['events'];

        $this->assertLessThanOrEqual($eventsArray, Fixture::pageSize());
        $this->assertNotNull($events['has_more']);
        $this->assertContainsOnlyInstancesOf(Event::class, $eventsArray);
    }

    /**
     * Test retrieving an event.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('events/retrieve.yml');

        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $event = self::$client->event->retrieve($events['events'][0]['id']);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test receiving (converting) an event.
     */
    public function testReceive()
    {
        $event = Util::receiveEvent(Fixture::eventJson());

        $this->assertInstanceOf(Event::class, $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test that we throw an error when bad input is received.
     */
    public function testReceiveBadInput()
    {
        $this->expectException(EasyPostException::class);

        Util::receiveEvent('bad input');
    }

    /**
     * Test that we throw an error when no input is received.
     */
    public function testReceiveNoInput()
    {
        $this->expectException(EasyPostException::class);

        Util::receiveEvent();
    }

    /**
     * Test retrieving all payloads for an event.
     */
    public function testRetrieveAllPayloads()
    {
        TestUtil::setupCassette('events/retrieve_all_payloads.yml');

        // Create a webhook to receive the event.
        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        // Create a batch to trigger an event.
        self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        // Wait for the event to be created.
        sleep(5);

        // Retrieve all events and extract the newest one.
        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);
        $event = $events['events'][0];

        // Retrieve all payloads for the event.
        $payloads = self::$client->event->retrieveAllPayloads(
            $event->id,
        );

        $payloadsArray = $payloads['payloads'];

        $this->assertContainsOnlyInstancesOf(Payload::class, $payloadsArray);

        // Delete the webhook.
        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test retrieving a payload for an event.
     */
    public function testRetrievePayload()
    {
        TestUtil::setupCassette('events/retrieve_payload.yml');

        // Create a webhook to receive the event.
        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        // Create a batch to trigger an event.
        self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        // Wait for the event to be created.
        sleep(5);

        // Retrieve all events and extract the newest one.
        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);
        $event = $events['events'][0];

        // Retrieve a specific payload for the event.
        // Payload does not exist due to queueing, so this will throw en exception.
        try {
             self::$client->event->retrievePayload(
                 $event->id,
                 'payload_11111111111111111111111111111111',
             );
        } catch (EasyPostException $error) {
            $this->assertEquals(404, $error->getHttpStatus());
        }

        // Invalid payload ID should throw an exception.
        try {
             self::$client->event->retrievePayload(
                 $event->id,
                 'payload_123',
             );
        } catch (EasyPostException $error) {
            $this->assertEquals(500, $error->getHttpStatus());
        }

        // Delete the webhook.
        self::$client->webhook->delete($webhook->id);
    }
}
