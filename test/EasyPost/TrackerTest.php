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
     * Test creating a Tracker.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('trackers/create.yml');

        $tracker = Tracker::create([
            "tracking_code" => "EZ1000000001",
        ]);

        $this->assertInstanceOf('\EasyPost\Tracker', $tracker);
        $this->assertStringMatchesFormat('trk_%s', $tracker->id);
        $this->assertEquals('pre_transit', $tracker->status);
    }

    /**
     * Test retrieving a Tracker.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('trackers/retrieve.yml');

        $tracker = Tracker::create([
            "tracking_code" => "EZ1000000001",
        ]);

        // Test trackers cycle through their "dummy" statuses automatically, the created and retrieved objects may differ
        $retrieved_tracker = Tracker::retrieve($tracker->id);

        $this->assertInstanceOf('\EasyPost\Tracker', $retrieved_tracker);
        $this->assertEquals($tracker->id, $retrieved_tracker->id);
    }

    /**
     * Test retrieving all trackers.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('trackers/all.yml');

        $trackers = Tracker::all([
            'page_size' => Fixture::page_size(),
        ]);

        $trackers_array = $trackers['trackers'];

        $this->assertLessThanOrEqual($trackers_array, Fixture::page_size());
        $this->assertNotNull($trackers['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Tracker', $trackers_array);
    }

    /**
     * Tests that we can create a list of bulk trackers with one request.
     *
     * @return void
     */
    public function testCreateList()
    {
        VCR::insertCassette('trackers/createList.yml');

        $response = Tracker::create_list([
            '0' => ["tracking_code" => "EZ1000000001"],
            '1' => ["tracking_code" => "EZ1000000002"],
            '2' => ["tracking_code" => "EZ1000000003"],
        ]);

        // This endpoint returns nothing so we assert the function returns true
        $this->assertEquals(true, $response);
    }
}
