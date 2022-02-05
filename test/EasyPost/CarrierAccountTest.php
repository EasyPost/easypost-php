<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\CarrierAccount;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

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
     * @return CarrierAccount
     */
    public function testCreate()
    {
        VCR::insertCassette('carrier_accounts/create.yml');

        $carrier_account = CarrierAccount::create([
            'type' => "UpsAccount",
            'credentials' => [
                'account_number' => "A1A1A1",
                'user_id' => "USERID",
                'password' => "PASSWORD",
                'access_license_number' => "ALN"
            ]
        ]);

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrier_account);
        $this->assertStringMatchesFormat('ca_%s', $carrier_account->id);

        // Return so other tests can reuse this object
        return $carrier_account;
    }

    /**
     * Test retrieving a carrier account.
     *
     * @param CarrierAccount $carrier_account
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(CarrierAccount $carrier_account)
    {
        VCR::insertCassette('carrier_accounts/retrieve.yml');

        $retrieved_carrier_account = CarrierAccount::retrieve($carrier_account->id);

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $retrieved_carrier_account);
        $this->assertStringMatchesFormat('ca_%s', $retrieved_carrier_account->id);
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

        foreach ($carrier_accounts as $carrier_account) {
            $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrier_account);
        }
    }

    /**
     * Test updating a carrier account.
     *
     * @param CarrierAccount $carrier_account
     * @return void
     * @depends testCreate
     */
    public function testUpdate(CarrierAccount $carrier_account)
    {
        VCR::insertCassette('carrier_accounts/update.yml');

        $test_description = 'My custom description';

        $carrier_account->description = $test_description;
        $carrier_account->save();

        $this->assertInstanceOf('\EasyPost\CarrierAccount', $carrier_account);
        $this->assertStringMatchesFormat('ca_%s', $carrier_account->id);
        $this->assertEquals($carrier_account->description, $test_description);
    }

    /**
     * Test deleting a carrier account.
     *
     * @param CarrierAccount $carrier_account
     * @return void
     * @depends testCreate
     */
    public function testDelete(CarrierAccount $carrier_account)
    {
        VCR::insertCassette('carrier_accounts/delete.yml');

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
