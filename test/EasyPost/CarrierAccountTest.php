<?php

namespace EasyPost\Test;

use EasyPost\CarrierAccount;
use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\MissingParameterException;

class CarrierAccountTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a carrier account.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('carrier_accounts/create.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

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
    public function testCreateWithoutType()
    {
        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

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
     * Test creating a carrier account with custom workflow such as FedEx or UPS.
     *
     * We purposefully don't pass data here because real data is required for this endpoint
     * which we don't have in a test context, simply assert the error matches when no data is passed.
     */
    public function testCreateWithCustomWorkflow()
    {
        TestUtil::setupCassette('carrier_accounts/createWithCustomWorkflow.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

        $data = [];
        $data['type'] = 'FedexAccount';
        // We have to send some registration data, otherwise API will throw a 400 Bad Request
        $data['registration_data'] = ['some' => 'data'];

        try {
            $carrierAccount = self::$client->carrierAccount->create($data);
            // Delete the carrier account once it's done being tested (should not be reached).
            self::$client->carrierAccount->delete($carrierAccount->id);
        } catch (ApiException $error) {
            $this->assertEquals(422, $error->getHttpStatus());
            $errorFound = false;
            $errors = $error->errors;
            foreach ($errors as $error) {
                if ($error['field'] == 'account_number' && $error['message'] == 'must be present and a string') {
                    $errorFound = true;
                    break;
                }
            }
        }

        $this->assertTrue($errorFound);
    }

    /**
     * Test retrieving a carrier account.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('carrier_accounts/retrieve.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

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
    public function testAll()
    {
        TestUtil::setupCassette('carrier_accounts/all.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccounts = self::$client->carrierAccount->all();

        $this->assertContainsOnlyInstancesOf(CarrierAccount::class, $carrierAccounts);
    }

    /**
     * Test updating a carrier account.
     */
    public function testUpdate()
    {
        TestUtil::setupCassette('carrier_accounts/update.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

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
    public function testDelete()
    {
        TestUtil::setupCassette('carrier_accounts/delete.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

        $carrierAccount = self::$client->carrierAccount->create(Fixture::basicCarrierAccount());

        try {
            self::$client->carrierAccount->delete($carrierAccount->id);
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown when we expected no error');
        }
    }

    /**
     * Test retrieving the carrier account types available.
     */
    public function testTypes()
    {
        TestUtil::setupCassette('carrier_accounts/types.yml');

        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));

        $types = self::$client->carrierAccount->types();

        $this->assertIsArray($types);
    }
}
