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
     * @return void
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
    }

    /**
     * Test retrieving an event.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('events/retrieve.yml');

        $events = Event::all([
            'page_size' => Fixture::page_size(),
        ]);

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
        $this->expectException(Error::class);

        Event::receive('bad input');
    }

    /**
     * Test that we throw an error when no input is received.
     *
     * @return void
     */
    public function testReceiveNoInput()
    {
        $this->expectException(Error::class);

        Event::receive();
    }
}
