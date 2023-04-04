<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\User;
use Exception;

class ReferralCustomerTest extends \PHPUnit\Framework\TestCase
{
    private static $client;
    private static $referralUserProdApiKey;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        $partnerUserProdApiKey = getenv('PARTNER_USER_PROD_API_KEY') !== false
            ? getenv('PARTNER_USER_PROD_API_KEY') : '123';
        self::$client = new EasyPostClient($partnerUserProdApiKey);
        self::$referralUserProdApiKey = getenv('REFERRAL_USER_PROD_API_KEY') !== false
            ? getenv('REFERRAL_USER_PROD_API_KEY') : '123';
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a child user.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('referral_customers/create.yml');

        $referral = self::$client->referralCustomer->create([
            'name' => 'Test Referral',
            'email' => 'test@test.com',
            'phone' => '8888888888'
        ]);

        $this->assertInstanceOf(User::class, $referral);
        $this->assertStringMatchesFormat('user_%s', $referral->id);
        $this->assertEquals('Test Referral', $referral->name);
    }

    /**
     * Test retrieving a child user.
     */
    public function testAll()
    {
        TestUtil::setupCassette('referral_customers/all.yml');

        $referralUsers = self::$client->referralCustomer->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $referralUsersArray = $referralUsers['referral_customers'];

        $this->assertLessThanOrEqual($referralUsersArray, Fixture::pageSize());
        $this->assertNotNull($referralUsers['has_more']);
        $this->assertContainsOnlyInstancesOf(User::class, $referralUsersArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage()
    {
        TestUtil::setupCassette('referral_customers/getNextPage.yml');

        try {
            $referralCustomers = self::$client->referralCustomer->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->referralCustomer->getNextPage($referralCustomers, Fixture::pageSize());

            $firstIdOfFirstPage = $referralCustomers['referral_customers'][0]->id;
            $secondIdOfSecondPage = $nextPage['referral_customers'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (Exception $error) {
            if (!($error instanceof EndOfPaginationException)) {
                throw new Exception('Test failed intentionally');
            }
            $this->assertTrue(true);
        }
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testUpdateEmail()
    {
        TestUtil::setupCassette('referral_customers/updateEmail.yml');

        $referralUsers = self::$client->referralCustomer->all();

        try {
            self::$client->referralCustomer->updateEmail(
                'email@example.com',
                $referralUsers['referral_customers'][0]['id']
            );
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test that we can add a credit card to a referral user.
     *
     * This test requires a partner user's production API key via PARTNER_USER_PROD_API_KEY
     * as well as one of that user's referral's production API keys via REFERRAL_USER_PROD_API_KEY.
     */
    public function testReferralAddCreditCard()
    {
        TestUtil::setupCassette('referral_customers/addCreditCard.yml');

        $creditCard = self::$client->referralCustomer->addCreditCard(
            self::$referralUserProdApiKey,
            Fixture::creditCardDetails()['number'],
            Fixture::creditCardDetails()['expiration_month'],
            Fixture::creditCardDetails()['expiration_year'],
            Fixture::creditCardDetails()['cvc']
        );

        $this->assertStringMatchesFormat('card_%s', $creditCard->id);
        $this->assertEquals('6170', $creditCard->last4);
    }
}
