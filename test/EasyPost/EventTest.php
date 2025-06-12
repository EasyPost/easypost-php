<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Event;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Payload;
use EasyPost\Util\Util;
use Exception;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
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
     * Test retrieving all events.
     */
    public function testAll(): void
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
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('events/getNextPage.yml');

        try {
            $events = self::$client->event->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->event->getNextPage($events, Fixture::pageSize());

            $firstIdOfFirstPage = $events['events'][0]->id;
            $secondIdOfSecondPage = $nextPage['events'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Test retrieving an event.
     */
    public function testRetrieve(): void
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
    public function testReceive(): void
    {
        $event = Util::receiveEvent(Fixture::eventJson());

        $this->assertInstanceOf(Event::class, $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test that we throw an error when bad input is received.
     */
    public function testReceiveBadInput(): void
    {
        $this->expectException(EasyPostException::class);

        Util::receiveEvent('bad input');
    }

    /**
     * Test that we throw an error when no input is received.
     */
    public function testReceiveNoInput(): void
    {
        $this->expectException(EasyPostException::class);

        Util::receiveEvent();
    }

    /**
     * Test retrieving all payloads for an event.
     */
    public function testRetrieveAllPayloads(): void
    {
        $cassetteName = 'events/retrieve_all_payloads.yml';
        // @phpstan-ignore-next-line
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        TestUtil::setupCassette($cassetteName);

        // Create a webhook to receive the event.
        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        // Create a batch to trigger an event.
        self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        if ($testRequiresWait === true) {
            sleep(5); // Wait for the event to be created.
        }

        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);
        $event = $events['events'][0];

        $payloads = self::$client->event->retrieveAllPayloads(
            $event->id,
        );

        $payloadsArray = $payloads['payloads'];

        $this->assertContainsOnlyInstancesOf(Payload::class, $payloadsArray);

        self::$client->webhook->delete($webhook->id);
    }

    /**
     * Test retrieving a payload for an event.
     */
    public function testRetrievePayload(): void
    {
        $cassetteName = 'events/retrieve_payload.yml';
        // @phpstan-ignore-next-line
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        TestUtil::setupCassette($cassetteName);

        // Create a webhook to receive the event.
        $webhook = self::$client->webhook->create([
            'url' => Fixture::webhookUrl(),
        ]);

        // Create a batch to trigger an event.
        self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        if ($testRequiresWait === true) {
            sleep(5); // Wait for the event to be created.
        }

        $events = self::$client->event->all([
            'page_size' => Fixture::pageSize(),
        ]);
        $event = $events['events'][0];

        // Payload does not exist due to queueing, so this will throw en exception.
        try {
            self::$client->event->retrievePayload(
                $event->id,
                'payload_11111111111111111111111111111111',
            );
        } catch (ApiException $error) {
            $this->assertEquals(404, $error->getHttpStatus());
        }

        self::$client->webhook->delete($webhook->id);
    }
}
