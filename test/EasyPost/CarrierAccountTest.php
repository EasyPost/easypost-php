<?php

namespace EasyPost\Test;

use EasyPost\CarrierAccount;
use EasyPost\Constant\Constants;
use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\MissingParameterException;
use PHPUnit\Framework\TestCase;

class CarrierAccountTest extends TestCase
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
     * @return array<string, array<mixed>>
     */
    public static function carrierAccountCreateUrlRoutingDataProvider(): array
    {
        $tests = [];

        foreach (Constants::CARRIER_ACCOUNT_CREATE_ENDPOINT_TYPES as $endpoint => $carrierAccountTypes) {
            foreach ($carrierAccountTypes as $carrierAccountType) {
                $tests[$carrierAccountType] = [
                    $carrierAccountType,
                    $endpoint,
                    self::carrierCreatePayloadForEndpoint($endpoint),
                ];
            }
        }

        return $tests;
    }

    /**
     * @param string $endpoint
     * @return array<string, mixed>
     */
    private static function carrierCreatePayloadForEndpoint(string $endpoint): array
    {
        if ($endpoint === '/carrier_accounts/open' || $endpoint === '/carrier_accounts/register') {
            return ['registration_data' => []];
        }

        return [];
    }

    /**
     * Test creating a carrier account.
     */
    public function testCreate(): void
    {
        TestUtil::setupCassette('carrier_accounts/create.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccount = self::$client->carrierAccount->create(Fixture::basicCarrierAccount());

        $this->assertEquals('DhlEcsAccount', $carrierAccount->type);
        $this->assertInstanceOf(CarrierAccount::class, $carrierAccount);
        $this->assertStringMatchesFormat('ca_%s', $carrierAccount->id);

        // Delete the carrier account once it's done being tested.
        self::$client->carrierAccount->delete($carrierAccount->id);
    }

    /**
     * Test creating a carrier account.
     */
    public function testCreateWithoutType(): void
    {
        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $params = Fixture::basicCarrierAccount();
        unset($params['type']);

        try {
            $carrierAccount = self::$client->carrierAccount->create($params);

            // Delete the carrier account once it's done being tested (should not be reached).
            self::$client->carrierAccount->delete($carrierAccount->id);
        } catch (MissingParameterException $error) {
            $this->assertEquals('type is required.', $error->getMessage());
        }
    }

    /**
     * Test creating carrier accounts with custom endpoint routing.
     *
     * @dataProvider carrierAccountCreateUrlRoutingDataProvider
     * @param array<string, mixed> $params
     */
    public function testCreateUrlRouting(
        string $carrierAccountType,
        string $expectedEndpoint,
        array $params
    ): void {
        $capturedRequest = [];
        TestUtil::setupCassette("carrier_accounts/create_url_routing_{$carrierAccountType}.yml");
        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));
        self::$client->requestEvent->addHandler(function (array $requestInfo) use (&$capturedRequest): void {
            if (!isset($capturedRequest['path']) && str_contains($requestInfo['path'], '/carrier_accounts')) {
                $capturedRequest = $requestInfo;
            }
        });

        $payload = ['type' => $carrierAccountType] + $params;
        $carrierAccount = null;
        try {
            $carrierAccount = self::$client->carrierAccount->create($payload);
        } catch (ApiException $error) {
            $this->assertNotContains($error->getHttpStatus(), [404, 500]);
        }

        if ($carrierAccount !== null) {
            $this->assertInstanceOf(CarrierAccount::class, $carrierAccount);
            $this->assertStringMatchesFormat('ca_%s', $carrierAccount->id);
            self::$client->carrierAccount->delete($carrierAccount->id);
        }

        $this->assertEquals(Constants::API_BASE . '/v2' . $expectedEndpoint, $capturedRequest['path']);
        if ($expectedEndpoint === '/carrier_accounts/register_oauth') {
            $this->assertArrayHasKey('carrier_account_oauth_registrations', $capturedRequest['request_body']);
        } else {
            $this->assertArrayHasKey('carrier_account', $capturedRequest['request_body']);
        }
    }

    /**
     * Test retrieving a carrier account.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('carrier_accounts/retrieve.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccount = self::$client->carrierAccount->create(Fixture::basicCarrierAccount());

        $retrievedCarrierAccount = self::$client->carrierAccount->retrieve($carrierAccount->id);

        $this->assertInstanceOf(CarrierAccount::class, $retrievedCarrierAccount);
        $this->assertEquals($carrierAccount, $retrievedCarrierAccount);

        // Delete the carrier account once it's done being tested.
        self::$client->carrierAccount->delete($retrievedCarrierAccount->id);
    }

    /**
     * Test retrieving all carrier accounts.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('carrier_accounts/all.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccounts = self::$client->carrierAccount->all();

        $this->assertContainsOnlyInstancesOf(CarrierAccount::class, $carrierAccounts);
    }

    /**
     * Test updating a carrier account.
     */
    public function testUpdate(): void
    {
        TestUtil::setupCassette('carrier_accounts/update.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccount = self::$client->carrierAccount->create(Fixture::basicCarrierAccount());

        $testDescription = 'My custom description';
        $params = ['description' => $testDescription];

        $updatedCarrierAccount = self::$client->carrierAccount->update($carrierAccount->id, $params);

        $this->assertInstanceOf(CarrierAccount::class, $updatedCarrierAccount);
        $this->assertStringMatchesFormat('ca_%s', $updatedCarrierAccount->id);
        $this->assertEquals($testDescription, $updatedCarrierAccount->description);

        // Delete the carrier account once it's done being tested.
        self::$client->carrierAccount->delete($updatedCarrierAccount->id);
    }

    /**
     * Test deleting a carrier account.
     */
    public function testDelete(): void
    {
        TestUtil::setupCassette('carrier_accounts/delete.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccount = self::$client->carrierAccount->create(Fixture::basicCarrierAccount());

        try {
            self::$client->carrierAccount->delete($carrierAccount->id);
            $this->expectNotToPerformAssertions();
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test retrieving the carrier account types available.
     */
    public function testTypes(): void
    {
        TestUtil::setupCassette('carrier_accounts/types.yml');

        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));

        $types = self::$client->carrierAccount->types();

        $this->assertIsArray($types);
    }
}
