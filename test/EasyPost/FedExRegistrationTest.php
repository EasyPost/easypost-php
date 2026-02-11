<?php

namespace EasyPost\Test;

use EasyPost\Constant\Constants;
use EasyPost\EasyPostClient;
use EasyPost\EasyPostObject;
use EasyPost\Test\Mocking\MockingUtility;
use EasyPost\Test\Mocking\MockRequest;
use EasyPost\Test\Mocking\MockRequestMatchRule;
use EasyPost\Test\Mocking\MockRequestResponseInfo;
use PHPUnit\Framework\TestCase;

class FedExRegistrationTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        $mockingUtility = new MockingUtility(
            [
                new MockRequest(
                    new MockRequestMatchRule(
                        'post',
                        '/v2\/fedex_registrations\/\S*\/address$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"email_address":null,"options":["SMS","CALL","INVOICE"],"phone_number":"***-***-9721"}'
                    )
                ),
                new MockRequest(
                    new MockRequestMatchRule(
                        'post',
                        '/v2\/fedex_registrations\/\S*\/pin$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"message":"sent secured Pin"}'
                    )
                ),
                new MockRequest(
                    new MockRequestMatchRule(
                        'post',
                        '/v2\/fedex_registrations\/\S*\/pin\/validate$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"id":"ca_123","type":"FedexAccount",' .
                        '"credentials":{"account_number":"123456789","mfa_key":"123456789-XXXXX"}}'
                    )
                ),
                new MockRequest(
                    new MockRequestMatchRule(
                        'post',
                        '/v2\/fedex_registrations\/\S*\/invoice$/'
                    ),
                    new MockRequestResponseInfo(
                        200,
                        '{"id":"ca_123","type":"FedexAccount",' .
                        '"credentials":{"account_number":"123456789","mfa_key":"123456789-XXXXX"}}'
                    )
                ),
            ]
        );

        self::$client = new EasyPostClient(
            (string)getenv('EASYPOST_TEST_API_KEY'),
            Constants::TIMEOUT,
            Constants::API_BASE,
            $mockingUtility
        );
    }

    /**
     * Test registering a billing address.
     */
    public function testRegisterAddress(): void
    {
        $fedexAccountNumber = '123456789';
        $params = [
            'address_validation' => [
                'name' => 'BILLING NAME',
                'street1' => '1234 BILLING STREET',
                'city' => 'BILLINGCITY',
                'state' => 'ST',
                'postal_code' => '12345',
                'country_code' => 'US',
            ],
            'easypost_details' => [
                'carrier_account_id' => 'ca_123',
            ],
        ];

        $response = self::$client->fedexRegistration->registerAddress($fedexAccountNumber, $params);

        $this->assertInstanceOf(EasyPostObject::class, $response);
        $this->assertNull($response->email_address); // @phpstan-ignore-line
        $this->assertNotNull($response->options); // @phpstan-ignore-line
        $this->assertContains('SMS', $response->options);
        $this->assertContains('CALL', $response->options);
        $this->assertContains('INVOICE', $response->options);
        $this->assertEquals('***-***-9721', $response->phone_number); // @phpstan-ignore-line
    }

    /**
     * Test requesting a pin.
     */
    public function testRequestPin(): void
    {
        $fedexAccountNumber = '123456789';

        $response = self::$client->fedexRegistration->requestPin($fedexAccountNumber, 'SMS');

        $this->assertInstanceOf(EasyPostObject::class, $response);
        $this->assertEquals('sent secured Pin', $response->message); // @phpstan-ignore-line
    }

    /**
     * Test validating a pin.
     */
    public function testValidatePin(): void
    {
        $fedexAccountNumber = '123456789';
        $params = [
            'pin_validation' => [
                'pin_code' => '123456',
                'name' => 'BILLING NAME',
            ],
            'easypost_details' => [
                'carrier_account_id' => 'ca_123',
            ],
        ];

        $response = self::$client->fedexRegistration->validatePin($fedexAccountNumber, $params);

        $this->assertInstanceOf(EasyPostObject::class, $response);
        $this->assertEquals('ca_123', $response->id); // @phpstan-ignore-line
        $this->assertEquals('FedexAccount', $response->type); // @phpstan-ignore-line
        $this->assertNotNull($response->credentials); // @phpstan-ignore-line
        $this->assertEquals('123456789', $response->credentials['account_number']);
        $this->assertEquals('123456789-XXXXX', $response->credentials['mfa_key']);
    }

    /**
     * Test submitting details about an invoice.
     */
    public function testSubmitInvoice(): void
    {
        $fedexAccountNumber = '123456789';
        $params = [
            'invoice_validation' => [
                'name' => 'BILLING NAME',
                'invoice_number' => 'INV-12345',
                'invoice_date' => '2025-12-08',
                'invoice_amount' => '100.00',
                'invoice_currency' => 'USD',
            ],
            'easypost_details' => [
                'carrier_account_id' => 'ca_123',
            ],
        ];

        $response = self::$client->fedexRegistration->submitInvoice($fedexAccountNumber, $params);

        $this->assertInstanceOf(EasyPostObject::class, $response);
        $this->assertEquals('ca_123', $response->id); // @phpstan-ignore-line
        $this->assertEquals('FedexAccount', $response->type); // @phpstan-ignore-line
        $this->assertNotNull($response->credentials); // @phpstan-ignore-line
        $this->assertEquals('123456789', $response->credentials['account_number']);
        $this->assertEquals('123456789-XXXXX', $response->credentials['mfa_key']);
    }
}
