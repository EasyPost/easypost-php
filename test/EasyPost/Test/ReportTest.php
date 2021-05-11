<?php

namespace EasyPost\Test;

use EasyPost\Report;
use EasyPost\EasyPost;

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

class TestReport extends \PHPUnit\Framework\TestCase
{
    /**
     * Report the creation of a Payment Log report
     *
     * @return void
     */
    public function testCreatePaymentLogReport()
    {
        // Set a random date to use
        $year = sprintf('%04d', rand(0001, 2020));
        $month = sprintf('%02d', rand(01, 12));

        // Build params and run assertions
        $params = array(
            "start_date" => "$year-$month-01",
            "end_date" => "$year-$month-31",
            "type" => "payment_log"
        );
        $payment_log_report = Report::create($params);
        $this->assertInstanceOf('\EasyPost\Report', $payment_log_report);
        $this->assertIsString($payment_log_report->id);
        $this->assertStringMatchesFormat("plrep_%s", $payment_log_report->id);

        return $payment_log_report;
    }

    /**
     * Report the creation of a Refund report
     *
     * @return void
     */
    public function testCreateRefundReport()
    {
        // Set a random date to use
        $year = sprintf('%04d', rand(0001, 2020));
        $month = sprintf('%02d', rand(01, 12));

        // Build params and run assertions
        $params = array(
            "start_date" => "$year-$month-01",
            "end_date" => "$year-$month-31",
            "type" => "refund"
        );
        $refund_report = Report::create($params);
        $this->assertInstanceOf('\EasyPost\Report', $refund_report);
        $this->assertIsString($refund_report->id);
        $this->assertStringMatchesFormat("refrep_%s", $refund_report->id);

        return $refund_report;
    }

    /**
     * Report the creation of a Shipment report
     *
     * @return void
     */
    public function testCreateShipmentReport()
    {
        // Set a random date to use
        $year = sprintf('%04d', rand(0001, 2020));
        $month = sprintf('%02d', rand(01, 12));

        // Build params and run assertions
        $params = array(
            "start_date" => "$year-$month-01",
            "end_date" => "$year-$month-31",
            "type" => "shipment"
        );
        $shipment_report = Report::create($params);
        $this->assertInstanceOf('\EasyPost\Report', $shipment_report);
        $this->assertIsString($shipment_report->id);
        $this->assertStringMatchesFormat("shprep_%s", $shipment_report->id);

        return $shipment_report;
    }

    /**
     * Report the creation of a Tracker report
     *
     * @return void
     */
    public function testCreateTrackerReport()
    {
        // Set a random date to use
        $year = sprintf('%04d', rand(0001, 2020));
        $month = sprintf('%02d', rand(01, 12));

        // Build params and run assertions
        $params = array(
            "start_date" => "$year-$month-01",
            "end_date" => "$year-$month-31",
            "type" => "tracker"
        );
        $tracker_report = Report::create($params);
        $this->assertInstanceOf('\EasyPost\Report', $tracker_report);
        $this->assertIsString($tracker_report->id);
        $this->assertStringMatchesFormat("trkrep_%s", $tracker_report->id);

        return $tracker_report;
    }

    /**
     * Report the retrieval of a Payment Log report
     *
     * @param Report $report
     * @return void
     * @depends testCreatePaymentLogReport
     */
    public function testRetrievePaymentLogReport($payment_log_report)
    {
        $retrieved_payment_log_report = Report::retrieve($payment_log_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_payment_log_report);
        $this->assertEquals($retrieved_payment_log_report->id, $payment_log_report->id);
        $this->assertEquals($retrieved_payment_log_report->start_date, $payment_log_report->start_date);
        $this->assertEquals($retrieved_payment_log_report->end_date, $payment_log_report->end_date);

        return $retrieved_payment_log_report;
    }

    /**
     * Report the retrieval of a Refund report
     *
     * @param Report $report
     * @return void
     * @depends testCreateRefundReport
     */
    public function testRetrieveRefundReport($refund_report)
    {
        $retrieved_refund_report = Report::retrieve($refund_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_refund_report);
        $this->assertEquals($retrieved_refund_report->id, $refund_report->id);
        $this->assertEquals($retrieved_refund_report->start_date, $refund_report->start_date);
        $this->assertEquals($retrieved_refund_report->end_date, $refund_report->end_date);

        return $retrieved_refund_report;
    }

    /**
     * Report the retrieval of a Shipment report
     *
     * @param Report $report
     * @return void
     * @depends testCreateShipmentReport
     */
    public function testRetrieveShipmentReport($shipment_report)
    {
        $retrieved_shipment_report = Report::retrieve($shipment_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_shipment_report);
        $this->assertEquals($retrieved_shipment_report->id, $shipment_report->id);
        $this->assertEquals($retrieved_shipment_report->start_date, $shipment_report->start_date);
        $this->assertEquals($retrieved_shipment_report->end_date, $shipment_report->end_date);

        return $retrieved_shipment_report;
    }

    /**
     * Report the retrieval of a Tracker report
     *
     * @param Report $report
     * @return void
     * @depends testCreateTrackerReport
     */
    public function testRetrieveTrackerReport($tracker_report)
    {
        $retrieved_tracker_report = Report::retrieve($tracker_report->id);

        $this->assertInstanceOf('\EasyPost\Report', $retrieved_tracker_report);
        $this->assertEquals($retrieved_tracker_report->id, $tracker_report->id);
        $this->assertEquals($retrieved_tracker_report->start_date, $tracker_report->start_date);
        $this->assertEquals($retrieved_tracker_report->end_date, $tracker_report->end_date);

        return $retrieved_tracker_report;
    }
}
