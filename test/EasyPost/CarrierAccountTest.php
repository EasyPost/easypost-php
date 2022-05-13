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
     * Test creating a carrier account.
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('carrier_accounts/create.yml');

        $carrier_account = CarrierAccount::create(Fixture::basic_carrier_account());

        $this->assertEquals('UpsAccount', $carrier_account->type);
        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrier_account);
        $this->assertStringMatchesFormat('ca_%s', $carrier_account->id);

        $carrier_account->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test retrieving a carrier account.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('carrier_accounts/retrieve.yml');

        $carrier_account = CarrierAccount::create(Fixture::basic_carrier_account());

        $retrieved_carrier_account = CarrierAccount::retrieve($carrier_account->id);

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $retrieved_carrier_account);
        $this->assertEquals($carrier_account, $retrieved_carrier_account);

        $carrier_account->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test retrieving all carrier accounts.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('carrier_accounts/all.yml');

        $carrier_accounts = CarrierAccount::all();

        $this->assertContainsOnlyInstancesOf('\EasyPost\CarrierAccount', $carrier_accounts);
    }

    /**
     * Test updating a carrier account.
     *
     * @return void
     */
    public function testUpdate()
    {
        VCR::insertCassette('carrier_accounts/update.yml');

        $carrier_account = CarrierAccount::create(Fixture::basic_carrier_account());

        $test_description = 'My custom description';

        $carrier_account->description = $test_description;
        $carrier_account->save();

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrier_account);
        $this->assertStringMatchesFormat('ca_%s', $carrier_account->id);
        $this->assertEquals($test_description, $carrier_account->description);

        $carrier_account->delete(); // Delete the carrier account once it's done being tested.
    }

    /**
     * Test deleting a carrier account.
     *
     * @return void
     */
    public function testDelete()
    {
        VCR::insertCassette('carrier_accounts/delete.yml');

        $carrier_account = CarrierAccount::create(Fixture::basic_carrier_account());

        $response = $carrier_account->delete();

        $this->assertIsObject($response);
    }

    /**
     * Test retrieving the carrier account types available.
     *
     * @return void
     */
    public function testTypes()
    {
        VCR::insertCassette('carrier_accounts/types.yml');

        $types = CarrierAccount::types();

        $this->assertIsArray($types);
    }
}
