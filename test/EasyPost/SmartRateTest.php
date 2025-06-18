<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class SmartRateTest extends TestCase
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
     * Test that we retrieve SmartRates when provided a from/to zip and planned ship date.
     */
    public function testRetrieveRecommendDate(): void
    {
        TestUtil::setupCassette('smartrate/recommendShipDate.yml');

        $params = [
            'from_zip' => Fixture::caAddress1()['zip'],
            'to_zip' => Fixture::caAddress2()['zip'],
            'desired_delivery_date' => Fixture::desiredDeliveryDate(),
            'carriers' => [Fixture::usps()],
        ];

        $rates = self::$client->smartRate->recommendShipDate($params);

        foreach ($rates['results'] as $entry) {
            $this->assertTrue(
                isset($entry['easypost_time_in_transit_data']),
                'Assertion failed: easypost_time_in_transit_data is not set.'
            );
        }
    }

    /**
     * Test that we retrieve SmartRates when provided a from/to zip and planned ship date.
     */
    public function testRetrieveEstimatedDeliveryDate(): void
    {
        TestUtil::setupCassette('smartrate/estimatedDeliveryDate.yml');

        $params = [
            'from_zip' => Fixture::caAddress1()['zip'],
            'to_zip' => Fixture::caAddress2()['zip'],
            'planned_ship_date' => Fixture::plannedShipDate(),
            'carriers' => [Fixture::usps()],
        ];

        $rates = self::$client->smartRate->estimateDeliveryDate($params);

        foreach ($rates['results'] as $entry) {
            $this->assertTrue(
                isset($entry['easypost_time_in_transit_data']),
                'Assertion failed: easypost_time_in_transit_data is not set.'
            );
        }
    }
}
