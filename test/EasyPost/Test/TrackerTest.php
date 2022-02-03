<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Tracker;
use EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));

class TrackerTest extends \PHPUnit\Framework\TestCase
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
     * Test the creation of a Tracker
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
        $this->assertIsString($tracker->id);
        $this->assertStringMatchesFormat('trk_%s', $tracker->id);
        $this->assertEquals($tracker->status, 'pre_transit');

        // Return so the `retrieve` test can reuse this object
        return $tracker;
    }

    /**
     * Test the retrieval of a Tracker
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
     * Test the retrieval of all trackers
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('trackers/all.yml');

        $page_size = 5;

        $trackers = Tracker::all([
            'page_size' => $page_size,
        ]);

        $trackers_array = $trackers['trackers'];
        $first_tracker = $trackers['trackers'][0];

        $this->assertIsArray($trackers_array);
        $this->assertCount($page_size, $trackers_array);
        $this->assertInstanceOf('\EasyPost\Tracker', $first_tracker);
        $this->assertIsString($first_tracker->id);
        $this->assertStringMatchesFormat('trk_%s', $first_tracker->id);
    }

    /**
     * Tests that we can create a list of bulk trackers with one request
     *
     * @return void
     */
    public function testCreateList()
    {
        VCR::insertCassette('trackers/createList.yml');

        Tracker::create_list([
            ["tracking_code" => "EZ1000000001"],
            ["tracking_code" => "EZ1000000002"],
            ["tracking_code" => "EZ1000000003"],
            ["tracking_code" => "EZ1000000004"],
        ]);

        // This endpoint/method does not return anything, just make sure the request doesn't fail
        $this->expectNotToPerformAssertions();
    }
}
