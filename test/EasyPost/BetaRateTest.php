<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Rate;
use EasyPost\Util\Util;
use PHPUnit\Framework\TestCase;

class BetaRateTest extends TestCase
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
     * Test retrieving stateless rates.
     */
    public function testRetrieveStatelessRate(): void
    {
        TestUtil::setupCassette('beta/rate/retrieveStatelessRates.yml');

        $statelessRates = self::$client->betaRate->retrieveStatelessRates(Fixture::basicShipment());

        $this->assertContainsOnlyInstancesOf(Rate::class, $statelessRates);
    }

    /**
     * Test retrieving lowest stateless rate.
     */
    public function testRetrieveLowestStatelessRate(): void
    {
        TestUtil::setupCassette('beta/rate/retrieveLowestStatelessRates.yml');

        $statelessRates = self::$client->betaRate->retrieveStatelessRates(Fixture::basicShipment());

        $lowestStatelessRate = Util::getLowestStatelessRate($statelessRates);

        $this->assertEquals('GroundAdvantage', $lowestStatelessRate->service);

        try {
            $lowestStatelessRate = Util::getLowestStatelessRate($statelessRates, ['invalidCarrier']);
        } catch (EasyPostException $error) {
            $this->assertEquals('No rates found.', $error->getMessage());
        }
    }
}
