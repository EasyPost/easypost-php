<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Billing;
use VCR\VCR;

class BillingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test funding a EasyPost wallet by using either primary or secondary payment method.
     *
     * @return void
     */
    public function testFundWallet()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/fundWallet.yml');

        $payment_method_fund = Billing::fund_wallet(2000, 'primary');

        $this->assertTrue($payment_method_fund);
    }

    /**
     * Test deleting a payment method.
     *
     * @return void
     */
    public function testDeletePaymentMethod()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/deletePaymentMethod.yml');

        $delete_payment_method = Billing::delete_payment_method('primary');

        $this->assertTrue($delete_payment_method);
    }

    /**
     * Test retrieving all payment methods.
     *
     * @return void
     */
    public function testRetrievePaymentMethods()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/retrievePaymentMethods.yml');

        $payment_methods = Billing::retrieve_payment_methods();

        $this->assertTrue($payment_methods->primary_payment_method != null);
        // $this->assertTrue($payment_methods->secondary_payment_method != null);
        // uncomment above assertion if there is a secondary payment in your account.
    }
}
