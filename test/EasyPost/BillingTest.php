<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Exception\Api\PaymentException;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Test\mocking\MockingUtility;
use EasyPost\Test\mocking\MockRequest;
use EasyPost\Test\mocking\MockRequestMatchRule;
use EasyPost\Test\mocking\MockRequestResponseInfo;

class BillingTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Set up the testing environment for this file.
     * @throws MissingParameterException
     */
    public static function setUpBeforeClass(): void
    {
        $mockingUtility = new MockingUtility(
            new MockRequest(
                new MockRequestMatchRule(
                    'post',
                    '/v2\\/bank_accounts\\/\\S*\\/charges$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{}'
                )
            ),
            new MockRequest(
                new MockRequestMatchRule(
                    'post',
                    '/v2\\/credit_cards\\/\\S*\\/charges$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{}'
                )
            ),
            new MockRequest(
                new MockRequestMatchRule(
                    'delete',
                    '/v2\\/bank_accounts\\/\\S*$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{}'
                )
            ),
            new MockRequest(
                new MockRequestMatchRule(
                    'delete',
                    '/v2\\/credit_cards\\/\\S*$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{}'
                )
            ),
            new MockRequest(
                new MockRequestMatchRule(
                    'get',
                    '/v2\\/payment_methods$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{"id": "summary_123", "primary_payment_method": {"id": "card_123", "last4": "1234"}, "secondary_payment_method": {"id": "bank_123", "bank_name": "Mock Bank"}}'
                )
            ),
        );

        // api key does not matter for mocking
        self::$client = self::getClient($mockingUtility);
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Test that the billing service can retrieve payment methods.
     *
     * @param MockingUtility|null $mockUtility
     * @return EasyPostClient
     * @throws MissingParameterException
     */
    public static function getClient(MockingUtility $mockUtility = null): \EasyPost\EasyPostClient
    {
        return new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'), Constants::TIMEOUT, Constants::API_BASE, $mockUtility);
    }

    public function testFundWallet()
    {
        try {
            self::$client->billing->fundWallet('2000', 'primary');
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    public function testFundWalletWithNoPriorityLevel()
    {
        try {
            self::$client->billing->fundWallet('2000');
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * @throws PaymentException
     */
    public function testRetrievePaymentMethodSummary()
    {
        $paymentMethodSummary = self::$client->billing->retrievePaymentMethods();
        $this->assertNotNull($paymentMethodSummary);
        $this->assertNotNull($paymentMethodSummary['primary_payment_method']);
        $this->assertNotNull($paymentMethodSummary['secondary_payment_method']);
    }

    /**
     * @throws MissingParameterException
     */
    public function testRetrievePaymentMethodSummaryNoId()
    {
        $mockingUtility = new MockingUtility(
            new MockRequest(
                new MockRequestMatchRule(
                    'get',
                    '/v2\\/payment_methods$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{"id": ""}' // no ID, will throw an error when we try to interace with this summary
                )
            )
        );
        $client = self::getClient($mockingUtility);

        try {
            $client->billing->retrievePaymentMethods();
            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testDeletePaymentMethod()
    {
        try {
            self::$client->billing->deletePaymentMethod('primary');
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    public function testGetPaymentMethodPrioritySwitchCase()
    {
        // Deleting a payment method gets the payment method internally, which should test the switch case.
        // The payment method is not exposed by this method, so we can't assert against it. If this test doesn't throw an exception, it worked (see test below)

        // testing "primary"
        try {
            self::$client->billing->deletePaymentMethod('primary');
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }

        // testing "secondary"
        try {
            self::$client->billing->deletePaymentMethod('secondary');
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }

        // testing "invalid"
        try {
            self::$client->billing->deletePaymentMethod('invalid');
            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    /**
     * @throws MissingParameterException
     */
    public function testGetPaymentMethodByPriorityNoPaymentMethod()
    {
        $mockingUtility = new MockingUtility(
            new MockRequest(
                new MockRequestMatchRule(
                    'get',
                    '/v2\\/payment_methods$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{"id": "summary_123", "primary_payment_method": null, "secondary_payment_method": null}' // null, will throw an error when we try to grab this payment method from the summary
                )
            )
        );

        $client = self::getClient($mockingUtility);

        // Deleting a payment method gets the payment method internally, which should execute the code that will trigger an exception.
        try {
            $client->billing->deletePaymentMethod('primary');
            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    /**
     * @throws MissingParameterException
     */
    public function testGetPaymentMethodByPriorityPaymentMethodNoId()
    {
        $mockingUtility = new MockingUtility(
            new MockRequest(
                new MockRequestMatchRule(
                    'get',
                    '/v2\\/payment_methods$/'
                ),
                new MockRequestResponseInfo(
                    200,
                    '{"id": "summary_123", "primary_payment_method": {"id": ""}, "secondary_payment_method": {"id": ""}}' // No ID, will throw an error when we try to grab this payment method from the summary
                )
            )
        );

        $client = self::getClient($mockingUtility);

        // Deleting a payment method gets the payment method internally, which should execute the code that will trigger an exception.
        try {
            $client->billing->deletePaymentMethod('primary');
            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }
}
