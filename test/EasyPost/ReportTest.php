<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Report;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

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
     * Test creating a Payment Log report.
     *
     * @return Report
     */
    public function testCreatePaymentLogReport()
    {
        VCR::insertCassette('reports/createPaymentLogReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_start_date(),
            'end_date' => Fixture::report_end_date(),
            'type' => 'payment_log'
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('plrep_%s', $report->id);

        // Return so other tests can reuse this object
        return $report;
    }

    /**
     * Test creating a Refund report.
     *
     * @return Report
     */
    public function testCreateRefundReport()
    {
        VCR::insertCassette('reports/createRefundReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_start_date(),
            'end_date' => Fixture::report_end_date(),
            'type' => 'refund'
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('refrep_%s', $report->id);

        // Return so other tests can reuse this object
        return $report;
    }

    /**
     * Test creating a Shipment report.
     *
     * @return Report
     */
    public function testCreateShipmentReport()
    {
        VCR::insertCassette('reports/createShipmentReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_start_date(),
            'end_date' => Fixture::report_end_date(),
            'type' => 'shipment'
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('shprep_%s', $report->id);

        // Return so other tests can reuse this object
        return $report;
    }

    /**
     * Test creating a Shipment Invoice report.
     *
     * @return Report
     */
    public function testCreateShipmentInvoiceReport()
    {
        VCR::insertCassette('reports/createShipmentInvoiceReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_start_date(),
            'end_date' => Fixture::report_end_date(),
            'type' => 'shipment_invoice'
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('shpinvrep_%s', $report->id);

        // Return so other tests can reuse this object
        return $report;
    }

    /**
     * Test creating a Tracker report.
     *
     * @return Report
     */
    public function testCreateTrackerReport()
    {
        VCR::insertCassette('reports/createTrackerReport.yml');

        $report = Report::create([
            'start_date' => Fixture::report_start_date(),
            'end_date' => Fixture::report_end_date(),
            'type' => 'tracker'
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $report);
        $this->assertStringMatchesFormat('trkrep_%s', $report->id);

        // Return so other tests can reuse this object
        return $report;
    }

    /**
     * Test retrieving a Payment Log report.
     *
     * @param Report $report
     * @return void
     * @depends testCreatePaymentLogReport
     */
    public function testRetrievePaymentLogReport($report)
    {
        VCR::insertCassette('reports/retrievePaymentLogReport.yml');

        $retrieved_report = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_report);
        $this->assertEquals($report->start_date, $retrieved_report->start_date);
        $this->assertEquals($report->end_date, $retrieved_report->end_date);
    }

    /**
     * Test retrieving a Refund report.
     *
     * @param Report $report
     * @return void
     * @depends testCreateRefundReport
     */
    public function testRetrieveRefundReport($report)
    {
        VCR::insertCassette('reports/retrieveRefundReport.yml');

        $retrieved_report = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_report);
        $this->assertEquals($report->start_date, $retrieved_report->start_date);
        $this->assertEquals($report->end_date, $retrieved_report->end_date);
    }

    /**
     * Test retrieving a Shipment report.
     *
     * @param Report $report
     * @return void
     * @depends testCreateShipmentReport
     */
    public function testRetrieveShipmentReport($report)
    {
        VCR::insertCassette('reports/retrieveShipmentReport.yml');

        $retrieved_report = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_report);
        $this->assertEquals($report->start_date, $retrieved_report->start_date);
        $this->assertEquals($report->end_date, $retrieved_report->end_date);
    }

    /**
     * Test retrieving a Shipment Invoice report.
     *
     * @param Report $report
     * @return void
     * @depends testCreateShipmentInvoiceReport
     */
    public function testRetrieveShipmentInvoiceReport($report)
    {
        VCR::insertCassette('reports/retrieveShipmentReport.yml');

        $retrieved_report = Report::retrieve($report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_report);
        $this->assertEquals($report->start_date, $retrieved_report->start_date);
        $this->assertEquals($report->end_date, $retrieved_report->end_date);
    }

    /**
     * Test retrieving a Tracker report.
     *
     * @param Report $report
     * @return void
     * @depends testCreateTrackerReport
     */
    public function testRetrieveTrackerReport($report)
    {
        VCR::insertCassette('reports/retrieveTrackerReport.yml');

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
