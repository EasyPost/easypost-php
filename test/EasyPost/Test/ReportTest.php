<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Report;
use EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));
define('REPORT_START_DATE', '2021-01-03');
define('REPORT_END_DATE', '2021-01-04');

class ReportTest extends \PHPUnit\Framework\TestCase
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
     * Test the creation of a Payment Log report
     *
     * @return Report
     */
    public function testCreatePaymentLogReport()
    {
        VCR::insertCassette('reports/createPaymentLogReport.yml');

        $payment_log_report = Report::create(array(
            "start_date" => REPORT_START_DATE,
            "end_date" => REPORT_END_DATE,
            "type" => "payment_log"
        ));

        $this->assertInstanceOf('\EasyPost\Report', $payment_log_report);
        $this->assertIsString($payment_log_report->id);
        $this->assertStringMatchesFormat("plrep_%s", $payment_log_report->id);

        // Return so the `retrieve` test can reuse this object
        return $payment_log_report;
    }

    /**
     * Test the creation of a Refund report
     *
     * @return Report
     */
    public function testCreateRefundReport()
    {
        VCR::insertCassette('reports/createRefundReport.yml');

        $refund_report = Report::create(array(
            "start_date" => REPORT_START_DATE,
            "end_date" => REPORT_END_DATE,
            "type" => "refund"
        ));

        $this->assertInstanceOf('\EasyPost\Report', $refund_report);
        $this->assertIsString($refund_report->id);
        $this->assertStringMatchesFormat("refrep_%s", $refund_report->id);

        // Return so the `retrieve` test can reuse this object
        return $refund_report;
    }

    /**
     * Test the creation of a Shipment report
     *
     * @return Report
     */
    public function testCreateShipmentReport()
    {
        VCR::insertCassette('reports/createShipmentReport.yml');

        $shipment_report = Report::create(array(
            "start_date" => REPORT_START_DATE,
            "end_date" => REPORT_END_DATE,
            "type" => "shipment"
        ));

        $this->assertInstanceOf('\EasyPost\Report', $shipment_report);
        $this->assertIsString($shipment_report->id);
        $this->assertStringMatchesFormat("shprep_%s", $shipment_report->id);

        // Return so the `retrieve` test can reuse this object
        return $shipment_report;
    }

    /**
     * Test the creation of a Shipment Invoice report
     *
     * @return Report
     */
    public function testCreateShipmentInvoiceReport()
    {
        VCR::insertCassette('reports/createShipmentInvoiceReport.yml');

        $shipment_invoice_report = Report::create(array(
            "start_date" => REPORT_START_DATE,
            "end_date" => REPORT_END_DATE,
            "type" => "shipment_invoice"
        ));

        $this->assertInstanceOf('\EasyPost\Report', $shipment_invoice_report);
        $this->assertIsString($shipment_invoice_report->id);
        $this->assertStringMatchesFormat("shpinvrep_%s", $shipment_invoice_report->id);

        // Return so the `retrieve` test can reuse this object
        return $shipment_invoice_report;
    }

    /**
     * Test the creation of a Tracker report
     *
     * @return Report
     */
    public function testCreateTrackerReport()
    {
        VCR::insertCassette('reports/createTrackerReport.yml');

        $tracker_report = Report::create(array(
            "start_date" => REPORT_START_DATE,
            "end_date" => REPORT_END_DATE,
            "type" => "tracker"
        ));

        $this->assertInstanceOf('\EasyPost\Report', $tracker_report);
        $this->assertIsString($tracker_report->id);
        $this->assertStringMatchesFormat("trkrep_%s", $tracker_report->id);

        // Return so the `retrieve` test can reuse this object
        return $tracker_report;
    }

    /**
     * Test the retrieval of a Payment Log report
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
        $this->assertEquals($retrieved_payment_log_report->id, $payment_log_report->id);
        $this->assertEquals($retrieved_payment_log_report->start_date, $payment_log_report->start_date);
        $this->assertEquals($retrieved_payment_log_report->end_date, $payment_log_report->end_date);
    }

    /**
     * Test the retrieval of a Refund report
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
        $this->assertEquals($retrieved_refund_report->id, $refund_report->id);
        $this->assertEquals($retrieved_refund_report->start_date, $refund_report->start_date);
        $this->assertEquals($retrieved_refund_report->end_date, $refund_report->end_date);
    }

    /**
     * Test the retrieval of a Shipment report
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
        $this->assertEquals($retrieved_shipment_report->id, $shipment_report->id);
        $this->assertEquals($retrieved_shipment_report->start_date, $shipment_report->start_date);
        $this->assertEquals($retrieved_shipment_report->end_date, $shipment_report->end_date);
    }

    /**
     * Test the retrieval of a Shipment Invoice report
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
        $this->assertEquals($retrieved_shipment_invoice_report->id, $shipment_invoice_report->id);
        $this->assertEquals($retrieved_shipment_invoice_report->start_date, $shipment_invoice_report->start_date);
        $this->assertEquals($retrieved_shipment_invoice_report->end_date, $shipment_invoice_report->end_date);
    }

    /**
     * Test the retrieval of a Tracker report
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
        $this->assertEquals($retrieved_tracker_report->id, $tracker_report->id);
        $this->assertEquals($retrieved_tracker_report->start_date, $tracker_report->start_date);
        $this->assertEquals($retrieved_tracker_report->end_date, $tracker_report->end_date);
    }
}
