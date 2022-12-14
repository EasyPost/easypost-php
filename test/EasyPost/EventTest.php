<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
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
        $this->assertContainsOnlyInstancesOf('\EasyPost\Event', $eventsArray);
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

        $this->assertInstanceOf('\EasyPost\Event', $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test receiving (converting) an event.
     */
    public function testReceive()
    {
        $event = Util::receiveEvent(Fixture::eventJson());

        $this->assertInstanceOf('\EasyPost\Event', $event);
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
}
