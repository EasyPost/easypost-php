<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Insurance;
use EasyPost\Exception\General\EndOfPaginationException;
use Exception;

class InsuranceTest extends \PHPUnit\Framework\TestCase
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
     * Test creating an insurance object.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('insurance/create.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $insuranceData = Fixture::basicInsurance();
        $insuranceData['tracking_code'] = $shipment->tracking_code;

        $insurance = self::$client->insurance->create($insuranceData);

        $this->assertInstanceOf(Insurance::class, $insurance);
        $this->assertStringMatchesFormat('ins_%s', $insurance->id);
        $this->assertEquals('100.00000', $insurance->amount);
    }

    /**
     * Test retrieving an insurance object.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('insurance/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $insuranceData = Fixture::basicInsurance();
        $insuranceData['tracking_code'] = $shipment->tracking_code;

        $insurance = self::$client->insurance->create($insuranceData);

        $retrievedInsurance = self::$client->insurance->retrieve($insurance->id);

        $this->assertInstanceOf(Insurance::class, $retrievedInsurance);
        $this->assertEquals($insurance, $retrievedInsurance);
    }

    /**
     * Test retrieving all insurance.
     */
    public function testAll()
    {
        TestUtil::setupCassette('insurance/all.yml');

        $insurance = self::$client->insurance->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $insuranceArray = $insurance['insurances'];

        $this->assertLessThanOrEqual($insuranceArray, Fixture::pageSize());
        $this->assertNotNull($insurance['has_more']);
        $this->assertContainsOnlyInstancesOf(Insurance::class, $insuranceArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('insurance/getNextPage.yml');

        try {
            $insurances = self::$client->insurance->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->insurance->getNextPage($insurances, Fixture::pageSize());

            $firstIdOfFirstPage = $insurances['insurances'][0]->id;
            $secondIdOfSecondPage = $nextPage['insurances'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
    }
}
