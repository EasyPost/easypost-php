<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Tracker;
use Exception;
use PHPUnit\Framework\TestCase;

class TrackerTest extends TestCase
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
     * Test creating a Tracker.
     */
    public function testCreate(): void
    {
        TestUtil::setupCassette('trackers/create.yml');

        $tracker = self::$client->tracker->create([
            'tracking_code' => 'EZ1000000001',
        ]);

        $this->assertInstanceOf(Tracker::class, $tracker);
        $this->assertStringMatchesFormat('trk_%s', $tracker->id);
        $this->assertEquals('pre_transit', $tracker->status);
    }

    /**
     * Test creating a Tracker when we don't wrap the param.
     */
    public function testCreateUnwrappedParam(): void
    {
        TestUtil::setupCassette('trackers/createUnwrappedParam.yml');

        $tracker = self::$client->tracker->create('EZ1000000001');

        $this->assertInstanceOf(Tracker::class, $tracker);
        $this->assertStringMatchesFormat('trk_%s', $tracker->id);
        $this->assertEquals('pre_transit', $tracker->status);
    }

    /**
     * Test retrieving a Tracker.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('trackers/retrieve.yml');

        $tracker = self::$client->tracker->create([
            'tracking_code' => 'EZ1000000001',
        ]);

        // Test trackers cycle through their "dummy" statuses automatically,
        // the created and retrieved objects may differ.
        $retrievedTracker = self::$client->tracker->retrieve($tracker->id);

        $this->assertInstanceOf(Tracker::class, $retrievedTracker);
        $this->assertEquals($tracker->id, $retrievedTracker->id);
    }

    /**
     * Test retrieving all trackers.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('trackers/all.yml');

        $trackers = self::$client->tracker->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $trackersArray = $trackers['trackers'];

        $this->assertLessThanOrEqual($trackersArray, Fixture::pageSize());
        $this->assertNotNull($trackers['has_more']);
        $this->assertContainsOnlyInstancesOf(Tracker::class, $trackersArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('trackers/getNextPage.yml');

        try {
            $trackers = self::$client->tracker->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->tracker->getNextPage($trackers, Fixture::pageSize());

            $firstIdOfFirstPage = $trackers['trackers'][0]->id;
            $secondIdOfSecondPage = $nextPage['trackers'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }
}
