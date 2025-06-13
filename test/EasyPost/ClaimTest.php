<?php

namespace EasyPost\Test;

use EasyPost\Claim;
use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use Exception;
use PHPUnit\Framework\TestCase;

class ClaimTest extends TestCase
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
     * Helper method to create and purchase an insured shipment.
     *
     * @param string $amount The amount of insurance for the shipment.
     * @return \EasyPost\Shipment The purchased shipment object.
     */
    private static function createAndBuyShipment(string $amount): \EasyPost\Shipment
    {
        $shipment = self::$client->shipment->create(Fixture::fullShipment());
        return self::$client->shipment->buy(
            $shipment->id,
            [
                'rate' => $shipment->lowestRate(),
                'insurance' => $amount,
            ]
        );
    }


    /**
     * Test creating an claim object.
     */
    public function testCreate(): void
    {
        TestUtil::setupCassette('claim/create.yml');
        $amount = '100';
        $purchasedShipment = self::createAndBuyShipment($amount);

        $claimData = Fixture::basicClaimData();
        $claimData['tracking_code'] = $purchasedShipment->tracking_code;
        $claimData['amount'] = $amount;

        $claim = self::$client->claim->create($claimData);

        $this->assertInstanceOf(Claim::class, $claim);
        $this->assertStringMatchesFormat('clm_%s', $claim->id);
    }

    /**
     * Test retrieving an claim object.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('claim/retrieve.yml');
        $amount = '100';
        $purchasedShipment = self::createAndBuyShipment($amount);

        $claimData = Fixture::basicClaimData();
        $claimData['tracking_code'] = $purchasedShipment->tracking_code;
        $claimData['amount'] = $amount;

        $claim = self::$client->claim->create($claimData);
        $retrievedClaim = self::$client->claim->retrieve($claim->id);

        $this->assertInstanceOf(Claim::class, $claim);
        $this->assertStringMatchesFormat('clm_%s', $claim->id);
        $this->assertEquals($claim->id, $retrievedClaim->id);
    }

    /**
     * Test retrieving all claims.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('claim/all.yml');

        $claim = self::$client->claim->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $claimArray = $claim['claims'];

        $this->assertLessThanOrEqual($claimArray, Fixture::pageSize());
        $this->assertNotNull($claim['has_more']);
        $this->assertContainsOnlyInstancesOf(Claim::class, $claimArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('claim/getNextPage.yml');

        try {
            $claims = self::$client->claim->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->claim->getNextPage($claims, Fixture::pageSize());

            $firstIdOfFirstPage = $claims['claims'][0]->id;
            $secondIdOfSecondPage = $nextPage['claims'][0]->id;

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
     * Test cancelling a filed claim.
     */
    public function testRefund(): void
    {
        TestUtil::setupCassette('claim/cancel.yml');
        $amount = '100';
        $purchasedShipment = self::createAndBuyShipment($amount);

        $claimData = Fixture::basicClaimData();
        $claimData['tracking_code'] = $purchasedShipment->tracking_code;
        $claimData['amount'] = $amount;

        $claim = self::$client->claim->create($claimData);
        $cancelledClaim = self::$client->claim->cancel($claim->id);

        $this->assertInstanceOf(Claim::class, $cancelledClaim);
        $this->assertStringMatchesFormat('clm_%s', $cancelledClaim->id);
        $this->assertEquals('cancelled', $cancelledClaim->status);
    }
}
