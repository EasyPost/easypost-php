<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Rate;
use EasyPost\Util\Util;

class BetaRateTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

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
     * Test retrieving stateless rates.
     */
    public function testRetrieveStatelessRate()
    {
        TestUtil::setupCassette('beta/rate/retrieveStatelessRates.yml');

        $statelessRates = self::$client->betaRate->retrieveStatelessRates(Fixture::basicShipment());

        $this->assertContainsOnlyInstancesOf(Rate::class, $statelessRates);
    }

    /**
     * Test retrieving lowest stateless rate.
     */
    public function testRetrieveLowestStatelessRate()
    {
        TestUtil::setupCassette('beta/rate/retrieveLowestStatelessRates.yml');

        $statelessRates = self::$client->betaRate->retrieveStatelessRates(Fixture::basicShipment());

        $lowestStatelessRate = Util::getLowestStatelessRate($statelessRates);

        $this->assertEquals('First', $lowestStatelessRate->service);

        try {
            $lowestStatelessRate = Util::getLowestStatelessRate($statelessRates, ['invalidCarrier']);
        } catch (EasyPostException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
