<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;

class BetaReferralCustomerTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        $referralCustomerProdApiKey = getenv('REFERRAL_CUSTOMER_PROD_API_KEY') !== false
            ? getenv('REFERRAL_CUSTOMER_PROD_API_KEY') : '123';
        self::$client = new EasyPostClient($referralCustomerProdApiKey);
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * This test requires a referral customer user's production API key via REFERRAL_CUSTOMER_PROD_API_KEY.
     *
     * We expect this test to fail because we don't have valid Stripe details to use. Assert the correct error.
     */
    public function testAddPaymentMethod()
    {
        TestUtil::setupCassette('beta/referral_customers/addPaymentMethod.yml');

        try {
            self::$client->betaReferralCustomer->addPaymentMethod(
                'cus_123',
                'ba_123',
                'primary'
            );
        } catch (ApiException $error) {
            $this->assertEquals('Invalid Payment Gateway Reference.', $error->getMessage());
        }
    }

    /**
     * This test requires a referral customer user's production API key via REFERRAL_CUSTOMER_PROD_API_KEY.
     *
     * We expect this test to fail because we don't have valid billing details to use. Assert the correct error.
     */
    public function testRefundByAmount()
    {
        TestUtil::setupCassette('beta/referral_customers/refundByAmount.yml');

        try {
            self::$client->betaReferralCustomer->refundByAmount(2000);
        } catch (ApiException $error) {
            $this->assertEquals(
                'Refund amount is invalid. Please use a valid amount or escalate to finance.',
                $error->getMessage()
            );
        }
    }

    /**
     * This test requires a referral customer user's production API key via REFERRAL_CUSTOMER_PROD_API_KEY.
     *
     * We expect this test to fail because we don't have valid billing details to use. Assert the correct error.
     */
    public function testRefundByPaymentLog()
    {
        TestUtil::setupCassette('beta/referral_customers/refundByPaymentLog.yml');

        try {
            self::$client->betaReferralCustomer->refundByPaymentLog('paylog_123');
        } catch (ApiException $error) {
            $this->assertEquals('We could not find a transaction with that id.', $error->getMessage());
        }
    }
}
