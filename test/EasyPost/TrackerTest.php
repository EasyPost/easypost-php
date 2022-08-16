<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use EasyPost\Tracker;
use VCR\VCR;

class TrackerTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Tracker.
     */
    public function testCreate()
    {
        VCR::insertCassette('trackers/create.yml');

        $tracker = Tracker::create([
            'tracking_code' => 'EZ1000000001',
        ]);

        $this->assertInstanceOf('\EasyPost\Tracker', $tracker);
        $this->assertStringMatchesFormat('trk_%s', $tracker->id);
        $this->assertEquals('pre_transit', $tracker->status);
    }

    /**
     * Test retrieving a Tracker.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('trackers/retrieve.yml');

        $tracker = Tracker::create([
            'tracking_code' => 'EZ1000000001',
        ]);

        // Test trackers cycle through their "dummy" statuses automatically, the created and retrieved objects may differ
        $retrievedTracker = Tracker::retrieve($tracker->id);

        $this->assertInstanceOf('\EasyPost\Tracker', $retrievedTracker);
        $this->assertEquals($tracker->id, $retrievedTracker->id);
    }

    /**
     * Test retrieving all trackers.
     */
    public function testAll()
    {
        VCR::insertCassette('trackers/all.yml');

        $trackers = Tracker::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $trackersArray = $trackers['trackers'];

        $this->assertLessThanOrEqual($trackersArray, Fixture::pageSize());
        $this->assertNotNull($trackers['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Tracker', $trackersArray);
    }

    /**
     * Tests that we can create a list of bulk trackers with one request.
     */
    public function testCreateList()
    {
        VCR::insertCassette('trackers/createList.yml');

        $response = Tracker::create_list([
            '0' => ['tracking_code' => 'EZ1000000001'],
            '1' => ['tracking_code' => 'EZ1000000002'],
            '2' => ['tracking_code' => 'EZ1000000003'],
        ]);

        // This endpoint returns nothing so we assert the function returns true
        $this->assertEquals(true, $response);
    }
}
