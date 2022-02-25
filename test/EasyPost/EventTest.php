<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Event;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class EventTest extends \PHPUnit\Framework\TestCase
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
     * Test retrieving all events.
     *
     * @return object
     */
    public function testAll()
    {
        VCR::insertCassette('events/all.yml');

        $events = Event::all([
            'page_size' => Fixture::page_size(),
        ]);

        $events_array = $events['events'];

        $this->assertLessThanOrEqual($events_array, Fixture::page_size());
        $this->assertNotNull($events['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Event', $events_array);

        // Return so other tests can reuse these objects
        return $events;
    }

    /**
     * Test retrieving an event.
     *
     * @param object $events
     * @return void
     * @depends testAll
     */
    public function testRetrieve(object $events)
    {
        VCR::insertCassette('events/retrieve.yml');

        $event = Event::retrieve($events['events'][0]);

        $this->assertInstanceOf('\EasyPost\Event', $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test receiving (converting) an event.
     *
     * @return void
     */
    public function testReceive()
    {
        $event = Event::receive(Fixture::event());

        $this->assertInstanceOf('\EasyPost\Event', $event);
        $this->assertStringMatchesFormat('evt_%s', $event->id);
    }

    /**
     * Test that we throw an error when bad input is received.
     *
     * @return void
     */
    public function testReceiveBadInput()
    {
        $this->expectException(\EasyPost\Error::class);

        Event::receive('bad input');
    }

    /**
     * Test that we throw an error when no input is received.
     *
     * @return void
     */
    public function testReceiveNoInput()
    {
        $this->expectException(\EasyPost\Error::class);

        Event::receive();
    }
}
