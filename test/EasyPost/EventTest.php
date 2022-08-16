<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Event;
use EasyPost\Test\Fixture;
use VCR\VCR;

class EventTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving all events.
     */
    public function testAll()
    {
        VCR::insertCassette('events/all.yml');

        $events = Event::all([
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
        VCR::insertCassette('events/retrieve.yml');

        $events = Event::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $event = Event::retrieve($events['events'][0]);

        $this->assertInstanceOf('\EasyPost\Event', $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test receiving (converting) an event.
     */
    public function testReceive()
    {
        $event = Event::receive(Fixture::eventJson());

        $this->assertInstanceOf('\EasyPost\Event', $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test that we throw an error when bad input is received.
     */
    public function testReceiveBadInput()
    {
        $this->expectException(Error::class);

        Event::receive('bad input');
    }

    /**
     * Test that we throw an error when no input is received.
     */
    public function testReceiveNoInput()
    {
        $this->expectException(Error::class);

        Event::receive();
    }
}
