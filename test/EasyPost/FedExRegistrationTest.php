<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\FedExAccountValidationResponse;
use EasyPost\FedExRequestPinResponse;
use PHPUnit\Framework\TestCase;

class FedExRegistrationTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test registering a billing address.
     */
    public function testRegisterAddress(): void
    {
        TestUtil::setupCassette('fedex_registration/registerAddress.yml');

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

        $this->assertInstanceOf(FedExAccountValidationResponse::class, $response);
        $this->assertNull($response->email_address);
        $this->assertNotNull($response->options);
        $this->assertContains('SMS', $response->options);
        $this->assertContains('CALL', $response->options);
        $this->assertContains('INVOICE', $response->options);
        $this->assertEquals('***-***-9721', $response->phone_number);
    }

    /**
     * Test requesting a pin.
     */
    public function testRequestPin(): void
    {
        TestUtil::setupCassette('fedex_registration/requestPin.yml');

        $fedexAccountNumber = '123456789';

        $response = self::$client->fedexRegistration->requestPin($fedexAccountNumber, 'SMS');

        $this->assertInstanceOf(FedExRequestPinResponse::class, $response);
        $this->assertEquals('sent secured Pin', $response->message);
    }

    /**
     * Test validating a pin.
     */
    public function testValidatePin(): void
    {
        TestUtil::setupCassette('fedex_registration/validatePin.yml');

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

        $this->assertInstanceOf(FedExAccountValidationResponse::class, $response);
        $this->assertEquals('ca_123', $response->id);
        $this->assertEquals('FedexAccount', $response->type);
        $this->assertNotNull($response->credentials);
        $this->assertEquals('123456789', $response->credentials['account_number']);
        $this->assertEquals('123456789-XXXXX', $response->credentials['mfa_key']);
    }

    /**
     * Test submitting details about an invoice.
     */
    public function testSubmitInvoice(): void
    {
        TestUtil::setupCassette('fedex_registration/submitInvoice.yml');

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

        $this->assertInstanceOf(FedExAccountValidationResponse::class, $response);
        $this->assertEquals('ca_123', $response->id);
        $this->assertEquals('FedexAccount', $response->type);
        $this->assertNotNull($response->credentials);
        $this->assertEquals('123456789', $response->credentials['account_number']);
        $this->assertEquals('123456789-XXXXX', $response->credentials['mfa_key']);
    }
}
