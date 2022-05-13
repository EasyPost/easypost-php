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
     * Test creating a report.
     *
     * @return Report
     */
    public function testCreateReport()
    {
        VCR::insertCassette('reports/createReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_date(),
            'end_date' => Fixture::report_date(),
            'type' => Fixture::report_type(),
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('shprep_%s', $report->id);
    }

    /**
     * Test creating a report with custom columns
     *
     * @return void
     */
    public function testCreateCustomColumnReport()
    {
        VCR::insertCassette('reports/createCustomColumnReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_date(),
            'end_date' => Fixture::report_date(),
            'type' => Fixture::report_type(),
            'columns' => ['usps_zone']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf('\EasyPost\Report', $report);
    }

    /**
     * Test creating a report with custom additional columns
     *
     * @return void
     */
    public function testCreateCustomAdditionalColumnReport()
    {
        VCR::insertCassette('reports/createCustomAdditionalColumnReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_date(),
            'end_date' => Fixture::report_date(),
            'type' => Fixture::report_type(),
            'additional_columns' => ['from_name', 'from_company']
        ]);

        // Reports are queued, so we can't retrieve it immediately.
        // Verifying columns would require parsing the CSV.
        // Verify parameters sent correctly by checking the URL in the cassette.
        $this->assertInstanceOf('\EasyPost\Report', $report);
    }

    /**
     * Test retrieving a report.
     *
     * @return void
     */
    public function testRetrieveReport()
    {
        VCR::insertCassette('reports/retrieveReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_date(),
            'end_date' => Fixture::report_date(),
            'type' => Fixture::report_type(),
        ]);

        $retrieved_report = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_report);
        $this->assertEquals($report->start_date, $retrieved_report->start_date);
        $this->assertEquals($report->end_date, $retrieved_report->end_date);
    }

    /**
     * Test retrieving all reports.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('reports/all.yml');

        $reports = Report::all([
            'type' => 'shipment',
            'page_size' => Fixture::page_size(),
        ]);

        $reports_array = $reports['reports'];

        $this->assertLessThanOrEqual($reports_array, Fixture::page_size());
        $this->assertNotNull($reports['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Report', $reports_array);
    }

    /**
     * Test throwing an error when creating a report with no report type set.
     *
     * @return void
     */
    public function testCreateNoType()
    {
        $this->expectException(\EasyPost\Error::class);

        Report::create();
    }

    /**
     * Test throwing an error when retrieving all reports with no report type set.
     *
     * @return void
     */
    public function testAllNoType()
    {
        $this->expectException(\EasyPost\Error::class);

        Report::all();
    }
}
