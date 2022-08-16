<?php

namespace EasyPost\Test;

use EasyPost\CarrierAccount;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use VCR\VCR;

class CarrierAccountTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a carrier account.
     */
    public function testCreate()
    {
        VCR::insertCassette('carrier_accounts/create.yml');

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
        VCR::insertCassette('carrier_accounts/retrieve.yml');

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
        VCR::insertCassette('carrier_accounts/all.yml');

        $carrierAccounts = CarrierAccount::all();

        $this->assertContainsOnlyInstancesOf('\EasyPost\CarrierAccount', $carrierAccounts);
    }

    /**
     * Test updating a carrier account.
     */
    public function testUpdate()
    {
        VCR::insertCassette('carrier_accounts/update.yml');

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
        VCR::insertCassette('carrier_accounts/delete.yml');

        $carrierAccount = CarrierAccount::create(Fixture::basicCarrierAccount());

        $response = $carrierAccount->delete();

        $this->assertIsObject($response);
    }

    /**
     * Test retrieving the carrier account types available.
     */
    public function testTypes()
    {
        VCR::insertCassette('carrier_accounts/types.yml');

        $types = CarrierAccount::types();

        $this->assertIsArray($types);
    }
}
