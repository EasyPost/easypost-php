<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Report;
use EasyPost\Test\Fixture;
use VCR\VCR;

class ReportTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a report.
     */
    public function testCreateReport()
    {
        VCR::insertCassette('reports/createReport.yml');

        $report = Report::create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('shprep_%s', $report->id);
    }

    /**
     * Test creating a report with custom columns
     */
    public function testCreateCustomColumnReport()
    {
        VCR::insertCassette('reports/createCustomColumnReport.yml');

        $report = Report::create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
            'columns' => ['usps_zone']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf('\EasyPost\Report', $report);
    }

    /**
     * Test creating a report with custom additional columns
     */
    public function testCreateCustomAdditionalColumnReport()
    {
        VCR::insertCassette('reports/createCustomAdditionalColumnReport.yml');

        $report = Report::create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
            'additional_columns' => ['from_name', 'from_company']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf('\EasyPost\Report', $report);
    }

    /**
     * Test retrieving a report.
     */
    public function testRetrieveReport()
    {
        VCR::insertCassette('reports/retrieveReport.yml');

        $report = Report::create([
            'start_date' => Fixture::reportDate(),
            'end_date' => Fixture::reportDate(),
            'type' => Fixture::reportType(),
        ]);

        $retrievedReport = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrievedReport);
        $this->assertEquals($report->start_date, $retrievedReport->start_date);
        $this->assertEquals($report->end_date, $retrievedReport->end_date);
    }

    /**
     * Test retrieving all reports.
     */
    public function testAll()
    {
        VCR::insertCassette('reports/all.yml');

        $reports = Report::all([
            'type' => 'shipment',
            'page_size' => Fixture::pageSize(),
        ]);

        $reportsArray = $reports['reports'];

        $this->assertLessThanOrEqual($reportsArray, Fixture::pageSize());
        $this->assertNotNull($reports['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Report', $reportsArray);
    }

    /**
     * Test throwing an error when creating a report with no report type set.
     */
    public function testCreateNoType()
    {
        $this->expectException(\EasyPost\Error::class);

        Report::create();
    }

    /**
     * Test throwing an error when retrieving all reports with no report type set.
     */
    public function testAllNoType()
    {
        $this->expectException(\EasyPost\Error::class);

        Report::all();
    }
}
