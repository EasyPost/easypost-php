<?php

namespace EasyPost\Test;

use EasyPost\CarrierAccount;

class CarrierAccountTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests('EASYPOST_PROD_API_KEY');
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

        $carrierAccount = CarrierAccount::create(Fixture::basicCarrierAccount());

        $this->assertEquals('DhlEcsAccount', $carrierAccount->type);
        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrierAccount);
        $this->assertStringMatchesFormat('ca_%s', $carrierAccount->id);

        $carrierAccount->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test retrieving a carrier account.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('carrier_accounts/retrieve.yml');

        $carrierAccount = CarrierAccount::create(Fixture::basicCarrierAccount());

        $retrievedCarrierAccount = CarrierAccount::retrieve($carrierAccount->id);

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $retrievedCarrierAccount);
        $this->assertEquals($carrierAccount, $retrievedCarrierAccount);

        $carrierAccount->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test retrieving all carrier accounts.
     */
    public function testAll()
    {
        TestUtil::setupCassette('carrier_accounts/all.yml');

        $carrierAccounts = CarrierAccount::all();

        $this->assertContainsOnlyInstancesOf('\EasyPost\CarrierAccount', $carrierAccounts);
    }

    /**
     * Test updating a carrier account.
     */
    public function testUpdate()
    {
        TestUtil::setupCassette('carrier_accounts/update.yml');

        $carrierAccount = CarrierAccount::create(Fixture::basicCarrierAccount());

        $testDescription = 'My custom description';

        $carrierAccount->description = $testDescription;
        $carrierAccount->save();

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrierAccount);
        $this->assertStringMatchesFormat('ca_%s', $carrierAccount->id);
        $this->assertEquals($testDescription, $carrierAccount->description);

        $carrierAccount->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test deleting a carrier account.
     */
    public function testDelete()
    {
        TestUtil::setupCassette('carrier_accounts/delete.yml');

        $carrierAccount = CarrierAccount::create(Fixture::basicCarrierAccount());

        $response = $carrierAccount->delete();

        $this->assertIsObject($response);
    }

    /**
     * Test retrieving the carrier account types available.
     */
    public function testTypes()
    {
        TestUtil::setupCassette('carrier_accounts/types.yml');

        $types = CarrierAccount::types();

        $this->assertIsArray($types);
    }
}
