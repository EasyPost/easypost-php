<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Test\Fixture;
use EasyPost\Test\Mocking\MockingUtility;
use EasyPost\Test\Mocking\MockRequest;
use EasyPost\Test\Mocking\MockRequestMatchRule;
use EasyPost\Test\Mocking\MockRequestResponseInfo;
use PHPUnit\Framework\TestCase;

class BillingTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Set up the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        $mockingUtility = new MockingUtility(
            [
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
                        '{"id": "summary_123", "primary_payment_method": {"id": "pm_123", "object": "CreditCard", "last4": "1234"}, "secondary_payment_method": {"id": "pm_123", "object": "BankAccount", "bank_name": "Mock Bank"}}' // phpcs:ignore
                    )
                ),
            ]
        );

        // api key does not matter for mocking
        self::$client = self::getClient($mockingUtility);
    }

    /**
     * Test that the billing service can retrieve payment methods.
     *
     * @param MockingUtility|null $mockUtility
     * @return EasyPostClient
     */
    public static function getClient(?MockingUtility $mockUtility = null): EasyPostClient
    {
        return new EasyPostClient(
            (string)getenv('EASYPOST_TEST_API_KEY'),
            Constants::TIMEOUT,
            Constants::API_BASE,
            $mockUtility
        );
    }

    /**
     * Test funding a wallet
     */
    public function testFundWallet(): void
    {
        try {
            self::$client->billing->fundWallet('2000', Fixture::billing()['priority'],);

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test funding a wallet without providing a priority level (default value)
     */
    public function testFundWalletWithNoPriorityLevel(): void
    {
        try {
            self::$client->billing->fundWallet('2000');

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test retrieving a payment method summary
     */
    public function testRetrievePaymentMethodSummary(): void
    {
        $paymentMethodSummary = self::$client->billing->retrievePaymentMethods();

        $this->assertNotNull($paymentMethodSummary);
        $this->assertNotNull($paymentMethodSummary['primary_payment_method']);
        $this->assertNotNull($paymentMethodSummary['secondary_payment_method']);
    }

    /**
     * Test retrieving a payment method summary that does not have an ID
     */
    public function testRetrievePaymentMethodSummaryNoId(): void
    {
        $mockingUtility = new MockingUtility(
            [
                new MockRequest(
                    new MockRequestMatchRule(
                        'get',
                        '/v2\\/payment_methods$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"id": ""}' // no ID, will throw an error when we try to interact with this summary
                    )
                )
            ]
        );
        $client = self::getClient($mockingUtility);

        try {
            $client->billing->retrievePaymentMethods();

            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->expectNotToPerformAssertions();
        }
    }

    /**
     * Test deleting a payment method
     */
    public function testDeletePaymentMethod(): void
    {
        try {
            self::$client->billing->deletePaymentMethod(Fixture::billing()['priority'],);

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test getting a payment method by priority level
     *
     * Deleting a payment method gets the payment method internally, which should test the switch case.
     * The payment method is not exposed by this method, so we can't assert against it.
     * If the function doesn't throw an exception, it worked.
     */
    public function testGetPaymentMethodPrioritySwitchCase(): void
    {
        // testing "primary"
        try {
            self::$client->billing->deletePaymentMethod(Fixture::billing()['priority'],);

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }

        // testing "secondary"
        try {
            self::$client->billing->deletePaymentMethod('secondary');

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }

        // testing "invalid"
        try {
            self::$client->billing->deletePaymentMethod('invalid');

            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->expectNotToPerformAssertions();
        }
    }

    /**
     * Test getting a payment method by priority level that has no payment method
     */
    public function testGetPaymentMethodByPriorityNoPaymentMethod(): void
    {
        $mockingUtility = new MockingUtility(
            [
                new MockRequest(
                    new MockRequestMatchRule(
                        'get',
                        '/v2\\/payment_methods$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        // null, will throw an error when we try to grab this payment method from the summary
                        '{"id": "summary_123", "primary_payment_method": null, "secondary_payment_method": null}'
                    )
                )
            ]
        );

        $client = self::getClient($mockingUtility);

        // Deleting a payment method gets the payment method internally, which should execute the
        // code that will trigger an exception.
        try {
            $client->billing->deletePaymentMethod(Fixture::billing()['priority'],);

            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->expectNotToPerformAssertions();
        }
    }

    /**
     * Test getting a payment method by priority level that has no payment method ID
     */
    public function testGetPaymentMethodByPriorityPaymentMethodNoId(): void
    {
        $mockingUtility = new MockingUtility(
            [
                new MockRequest(
                    new MockRequestMatchRule(
                        'get',
                        '/v2\\/payment_methods$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        // No ID, will throw an error when we try to grab this payment method from the summary
                        '{"id": "summary_123", "primary_payment_method": {"id": ""}, "secondary_payment_method": {"id": ""}}' // phpcs:ignore
                    )
                )
            ]
        );

        $client = self::getClient($mockingUtility);

        // Deleting a payment method gets the payment method internally, which should execute the
        // code that will trigger an exception.
        try {
            $client->billing->deletePaymentMethod(Fixture::billing()['priority'],);

            $this->fail('Exception not thrown when we expected one');
        } catch (\Exception $exception) {
            $this->expectNotToPerformAssertions();
        }
    }

    /**
     * Test determining the payment method type by object type
     */
    public function testGetPaymentMethodInfoByObjectType(): void
    {
        $mockingUtility = new MockingUtility(
            [
                new MockRequest(
                    new MockRequestMatchRule(
                        'get',
                        '/v2\\/payment_methods$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"id": "summary_123", "primary_payment_method": {"id": "pm_123", "object": "CreditCard", "last4": "1234"}, "secondary_payment_method": {"id": "pm_123", "object": "BankAccount", "bank_name": "Mock Bank"}}' // phpcs:ignore
                    )
                ),
                new MockRequest(
                    new MockRequestMatchRule(
                        'delete',
                        '/v2\\/credit_cards\\/pm_123$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{}'
                    )
                ),
            ]
        );
        $client = self::getClient($mockingUtility);

        // getPaymentInfo is private, but we can test it by attempting to delete a payment method
        // only a delete request to /v2/credit_cards/pm_123 is mocked
        // if the delete function works, it's because it found the correct payment method type
        try {
            $client->billing->deletePaymentMethod(Fixture::billing()['priority'],);

            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }
}
