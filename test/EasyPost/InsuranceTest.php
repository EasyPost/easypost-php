<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Insurance;
use Exception;
use PHPUnit\Framework\TestCase;

class InsuranceTest extends TestCase
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
     * Test creating an insurance object.
     */
    public function testCreate(): void
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
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('insurance/retrieve.yml');

        $insurances = self::$client->insurance->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $insuranceArray = $insurances['insurances'];

        $retrievedInsurance = self::$client->insurance->retrieve($insuranceArray[0]['id']);

        $this->assertInstanceOf(Insurance::class, $retrievedInsurance);
        $this->assertEquals($insuranceArray[0], $retrievedInsurance);
    }

    /**
     * Test retrieving all insurance.
     */
    public function testAll(): void
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
    public function testGetNextPage(): void
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
            $this->assertNotNull($nextPage['_params']);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Test refunding an standalone insurance.
     */
    public function testRefund(): void
    {
        TestUtil::setupCassette('insurance/refund.yml');

        $insuranceData = Fixture::basicInsurance();
        $insuranceData['tracking_code'] = 'EZ1000000001';

        $insurance = self::$client->insurance->create($insuranceData);
        $cancelledInsurance = self::$client->insurance->refund($insurance->id);

        $this->assertInstanceOf(Insurance::class, $cancelledInsurance);
        $this->assertStringMatchesFormat('ins_%s', $cancelledInsurance->id);
        $this->assertEquals('cancelled', $cancelledInsurance->status);
        $this->assertEquals('Insurance was cancelled by the user.', $cancelledInsurance->messages[0]);
    }
}
