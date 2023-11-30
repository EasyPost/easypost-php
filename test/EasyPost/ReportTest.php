<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Report;
use Exception;

class ReportTest extends \PHPUnit\Framework\TestCase
{
    private static EasyPostClient $client;

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
     * Test creating a report.
     */
    public function testCreateReport(): void
    {
        TestUtil::setupCassette('reports/createReport.yml');

        $report = self::$client->report->create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
        ]);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertStringMatchesFormat('shprep_%s', $report->id);
    }

    /**
     * Test creating a report with custom columns
     */
    public function testCreateCustomColumnReport(): void
    {
        TestUtil::setupCassette('reports/createCustomColumnReport.yml');

        $report = self::$client->report->create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
            'columns' => ['usps_zone']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf(Report::class, $report);
    }

    /**
     * Test creating a report with custom additional columns
     */
    public function testCreateCustomAdditionalColumnReport(): void
    {
        TestUtil::setupCassette('reports/createCustomAdditionalColumnReport.yml');

        $report = self::$client->report->create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
            'additional_columns' => ['from_name', 'from_company']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf(Report::class, $report);
    }

    /**
     * Test retrieving a report.
     */
    public function testRetrieveReport(): void
    {
        TestUtil::setupCassette('reports/retrieveReport.yml');

        $report = self::$client->report->create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
        ]);

        $retrievedReport = self::$client->report->retrieve($report->id);

        $this->assertInstanceOf(Report::class, $retrievedReport);
        $this->assertEquals($report->start_date, $retrievedReport->start_date);
        $this->assertEquals($report->end_date, $retrievedReport->end_date);
    }

    /**
     * Test retrieving all reports.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('reports/all.yml');

        $reports = self::$client->report->all([
            'type' => 'shipment',
            'page_size' => Fixture::pageSize(),
        ]);

        $reportsArray = $reports['reports'];

        $this->assertLessThanOrEqual($reportsArray, Fixture::pageSize());
        $this->assertNotNull($reports['has_more']);
        $this->assertContainsOnlyInstancesOf(Report::class, $reportsArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('reports/getNextPage.yml');

        try {
            $reports = self::$client->report->all([
                'type' => 'shipment',
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->report->getNextPage($reports, Fixture::pageSize());

            $firstIdOfFirstPage = $reports['reports'][0]->id;
            $secondIdOfSecondPage = $nextPage['reports'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->assertTrue(true);
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Test throwing an error when creating a report with no report type set.
     */
    public function testCreateNoType(): void
    {
        $this->expectException(MissingParameterException::class);

        self::$client->report->create();
    }

    /**
     * Test throwing an error when retrieving all reports with no report type set.
     */
    public function testAllNoType(): void
    {
        $this->expectException(MissingParameterException::class);

        self::$client->report->all();
    }
}
