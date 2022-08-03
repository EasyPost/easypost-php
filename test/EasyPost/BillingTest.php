<?php

namespace EasyPost\Test;

use EasyPost\Billing;
use EasyPost\EasyPost;
use VCR\VCR;

class BillingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test funding a EasyPost wallet by using either primary or secondary payment method.
     */
    public function testFundWallet()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/fundWallet.yml');

        $fundSuccess = Billing::fund_wallet(2000, 'primary');

        $this->assertTrue($fundSuccess);
    }

    /**
     * Test deleting a payment method.
     */
    public function testDeletePaymentMethod()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/deletePaymentMethod.yml');

        $deletedPaymentMethod = Billing::delete_payment_method('primary');

        $this->assertTrue($deletedPaymentMethod);
    }

    /**
     * Test retrieving all payment methods.
     */
    public function testRetrievePaymentMethods()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/retrievePaymentMethods.yml');

        $paymentMethods = Billing::retrieve_payment_methods();

        $this->assertTrue($paymentMethods->primary_payment_method != null);
        // $this->assertTrue($paymentMethods->secondary_payment_method != null);
        // uncomment above assertion if there is a secondary payment in your account.
    }
}
