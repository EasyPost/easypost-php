<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Report;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class ReportTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Payment Log report.
     *
     * @return Report
     */
    public function testCreatePaymentLogReport()
    {
        VCR::insertCassette('reports/createPaymentLogReport.yml');

        $payment_log_report = Report::create([
            "start_date" => Fixture::report_start_date(),
            "end_date" => Fixture::report_end_date(),
            "type" => "payment_log"
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $payment_log_report);
        $this->assertStringMatchesFormat("plrep_%s", $payment_log_report->id);

        // Return so the `retrieve` test can reuse this object
        return $payment_log_report;
    }

    /**
     * Test creating a Refund report.
     *
     * @return Report
     */
    public function testCreateRefundReport()
    {
        VCR::insertCassette('reports/createRefundReport.yml');

        $refund_report = Report::create([
            "start_date" => Fixture::report_start_date(),
            "end_date" => Fixture::report_end_date(),
            "type" => "refund"
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $refund_report);
        $this->assertStringMatchesFormat("refrep_%s", $refund_report->id);

        // Return so the `retrieve` test can reuse this object
        return $refund_report;
    }

    /**
     * Test creating a Shipment report.
     *
     * @return Report
     */
    public function testCreateShipmentReport()
    {
        VCR::insertCassette('reports/createShipmentReport.yml');

        $shipment_report = Report::create([
            "start_date" => Fixture::report_start_date(),
            "end_date" => Fixture::report_end_date(),
            "type" => "shipment"
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $shipment_report);
        $this->assertStringMatchesFormat("shprep_%s", $shipment_report->id);

        // Return so the `retrieve` test can reuse this object
        return $shipment_report;
    }

    /**
     * Test creating a Shipment Invoice report.
     *
     * @return Report
     */
    public function testCreateShipmentInvoiceReport()
    {
        VCR::insertCassette('reports/createShipmentInvoiceReport.yml');

        $shipment_invoice_report = Report::create([
            "start_date" => Fixture::report_start_date(),
            "end_date" => Fixture::report_end_date(),
            "type" => "shipment_invoice"
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $shipment_invoice_report);
        $this->assertStringMatchesFormat("shpinvrep_%s", $shipment_invoice_report->id);

        // Return so the `retrieve` test can reuse this object
        return $shipment_invoice_report;
    }

    /**
     * Test creating a Tracker report.
     *
     * @return Report
     */
    public function testCreateTrackerReport()
    {
        VCR::insertCassette('reports/createTrackerReport.yml');

        $tracker_report = Report::create([
            "start_date" => Fixture::report_start_date(),
            "end_date" => Fixture::report_end_date(),
            "type" => "tracker"
        ]);

        $this->assertInstanceOf('\EasyPost\Report', $tracker_report);
        $this->assertStringMatchesFormat("trkrep_%s", $tracker_report->id);

        // Return so the `retrieve` test can reuse this object
        return $tracker_report;
    }

    /**
     * Test retrieving a Payment Log report.
     *
     * @param Report $payment_log_report
     * @return void
     * @depends testCreatePaymentLogReport
     */
    public function testRetrievePaymentLogReport($payment_log_report)
    {
        VCR::insertCassette('reports/retrievePaymentLogReport.yml');

        $retrieved_payment_log_report = Report::retrieve($payment_log_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_payment_log_report);
        $this->assertEquals($retrieved_payment_log_report->start_date, $payment_log_report->start_date);
        $this->assertEquals($retrieved_payment_log_report->end_date, $payment_log_report->end_date);
    }

    /**
     * Test retrieving a Refund report.
     *
     * @param Report $refund_report
     * @return void
     * @depends testCreateRefundReport
     */
    public function testRetrieveRefundReport($refund_report)
    {
        VCR::insertCassette('reports/retrieveRefundReport.yml');

        $retrieved_refund_report = Report::retrieve($refund_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_refund_report);
        $this->assertEquals($retrieved_refund_report->start_date, $refund_report->start_date);
        $this->assertEquals($retrieved_refund_report->end_date, $refund_report->end_date);
    }

    /**
     * Test retrieving a Shipment report.
     *
     * @param Report $shipment_report
     * @return void
     * @depends testCreateShipmentReport
     */
    public function testRetrieveShipmentReport($shipment_report)
    {
        VCR::insertCassette('reports/retrieveShipmentReport.yml');

        $retrieved_shipment_report = Report::retrieve($shipment_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_shipment_report);
        $this->assertEquals($retrieved_shipment_report->start_date, $shipment_report->start_date);
        $this->assertEquals($retrieved_shipment_report->end_date, $shipment_report->end_date);
    }

    /**
     * Test retrieving a Shipment Invoice report.
     *
     * @param Report $shipment_invoice_report
     * @return void
     * @depends testCreateShipmentInvoiceReport
     */
    public function testRetrieveShipmentInvoiceReport($shipment_invoice_report)
    {
        VCR::insertCassette('reports/retrieveShipmentReport.yml');

        $retrieved_shipment_invoice_report = Report::retrieve($shipment_invoice_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_shipment_invoice_report);
        $this->assertEquals($retrieved_shipment_invoice_report->start_date, $shipment_invoice_report->start_date);
        $this->assertEquals($retrieved_shipment_invoice_report->end_date, $shipment_invoice_report->end_date);
    }

    /**
     * Test retrieving a Tracker report.
     *
     * @param Report $tracker_report
     * @return void
     * @depends testCreateTrackerReport
     */
    public function testRetrieveTrackerReport($tracker_report)
    {
        VCR::insertCassette('reports/retrieveTrackerReport.yml');

        $retrieved_tracker_report = Report::retrieve($tracker_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_tracker_report);
        $this->assertEquals($retrieved_tracker_report->start_date, $tracker_report->start_date);
        $this->assertEquals($retrieved_tracker_report->end_date, $tracker_report->end_date);
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
        foreach ($reports_array as $report) {
            $this->assertInstanceOf('\EasyPost\Report', $report);
        }
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
