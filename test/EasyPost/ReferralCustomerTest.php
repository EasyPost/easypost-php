<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\User;
use Exception;
use PHPUnit\Framework\TestCase;

class ReferralCustomerTest extends TestCase
{
    private static EasyPostClient $client;
    private static string $referralUserProdApiKey;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        $partnerUserProdApiKey = getenv('PARTNER_USER_PROD_API_KEY') !== false
            ? (string)getenv('PARTNER_USER_PROD_API_KEY') : '123';
        self::$client = new EasyPostClient($partnerUserProdApiKey);
        self::$referralUserProdApiKey = getenv('REFERRAL_CUSTOMER_PROD_API_KEY') !== false
            ? (string)getenv('REFERRAL_CUSTOMER_PROD_API_KEY') : '123';
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
    public function testCreate(): void
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
    public function testAll(): void
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
    public function testGetNextPage(): void
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
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testUpdateEmail(): void
    {
        TestUtil::setupCassette('referral_customers/updateEmail.yml');

        $referralUsers = self::$client->referralCustomer->all();

        try {
            self::$client->referralCustomer->updateEmail(
                $referralUsers['referral_customers'][0]['id'],
                'email@example.com',
            );
            $this->expectNotToPerformAssertions();
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
    public function testAddCreditCard(): void
    {
        TestUtil::setupCassette('referral_customers/addCreditCard.yml');

        $creditCard = self::$client->referralCustomer->addCreditCard(
            self::$referralUserProdApiKey,
            Fixture::creditCardDetails()['number'],
            Fixture::creditCardDetails()['expiration_month'],
            Fixture::creditCardDetails()['expiration_year'],
            Fixture::creditCardDetails()['cvc']
        );

        $this->assertStringMatchesFormat('pm_%s', $creditCard->id);
        $this->assertEquals('6170', $creditCard->last4);
    }

    /**
     * Test that we can add a credit card to a referral user.
     *
     * This test requires a referral customer's production API key via REFERRAL_CUSTOMER_PROD_API_KEY.
     * We expect this test to fail because we don't have valid billing details to use. Assert the correct error.
     */
    public function testAddCreditCardFromStripe(): void
    {
        TestUtil::setupCassette('referral_customers/addCreditCardFromStripe.yml');

        try {
            self::$client->referralCustomer->addCreditCardFromStripe(
                self::$referralUserProdApiKey,
                Fixture::billing()['payment_method_id'],
                Fixture::billing()['priority'],
            );
        } catch (ApiException $error) {
            $this->assertEquals(
                'Stripe::PaymentMethod does not exist for the specified reference_id',
                $error->getMessage()
            );
        }
    }

    /**
     * Test that we can add a bank account to a referral user.
     *
     * This test requires a referral customer's production API key via REFERRAL_CUSTOMER_PROD_API_KEY.
     * We expect this test to fail because we don't have valid billing details to use. Assert the correct error.
     */
    public function testAddBankAccountFromStripe(): void
    {
        TestUtil::setupCassette('referral_customers/addBankAccountFromStripe.yml');

        try {
            self::$client->referralCustomer->addBankAccountFromStripe(
                self::$referralUserProdApiKey,
                Fixture::billing()['financial_connections_id'],
                Fixture::billing()['mandate_data'],
                Fixture::billing()['priority'],
            );
        } catch (ApiException $error) {
            $this->assertEquals(
                'account_holder_name must be present when creating a Financial Connections payment method',
                $error->getMessage()
            );
        }
    }
}
