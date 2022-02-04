<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Tracker;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class TrackerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests.
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
        $this->assertEquals($tracker->status, 'pre_transit');

        // Return so the `retrieve` test can reuse this object
        return $tracker;
    }

    /**
     * Test retrieving a Tracker.
     *
     * @param Tracker $tracker
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Tracker $tracker)
    {
        VCR::insertCassette('trackers/retrieve.yml');

        // Test trackers cycle through their "dummy" statuses automatically, the created and retrieved objects may differ
        $retrieved_tracker = Tracker::retrieve($tracker->id);

        $this->assertInstanceOf('\EasyPost\Tracker', $retrieved_tracker);
        $this->assertEquals($retrieved_tracker->id, $tracker->id);
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
        foreach ($trackers_array as $tracker) {
            $this->assertInstanceOf('\EasyPost\Tracker', $tracker);
        }
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
            ["tracking_code" => "EZ1000000001"],
            ["tracking_code" => "EZ1000000002"],
            ["tracking_code" => "EZ1000000003"],
        ]);

        // This endpoint returns nothing so we assert the function returns true
        $this->assertEquals(true, $response);
    }
}
