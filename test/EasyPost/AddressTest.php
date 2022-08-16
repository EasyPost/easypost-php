<?php

namespace EasyPost\Test;

use EasyPost\Address;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;
use VCR\VCR;

class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

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
     * Test creating an address.
     */
    public function testCreate()
    {
        VCR::insertCassette('addresses/create.yml');

        $address = Address::create(Fixture::caAddress1());

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 Townsend St', $address->street1);
    }

    /**
     * Test creating a verified address.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateVerify()
    {
        VCR::insertCassette('addresses/createVerify.yml');

        $addressData = Fixture::incorrectAddress();
        $addressData['verify'] = true;

        $address = Address::create($addressData);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('417 MONTGOMERY ST FL 5', $address->street1);
        $this->assertEquals('Invalid secondary information(Apt/Suite#)', $address->verifications->zip4->errors[0]->message);
    }

    /**
     * Test creating an address with verify_strict param.
     */
    public function testCreateVerifyStrict()
    {
        VCR::insertCassette('addresses/createVerifyStrict.yml');

        $addressData = Fixture::caAddress1();
        $addressData['verify_strict'] = true;

        $address = Address::create($addressData);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $address->street1);
    }

    /**
     * Test creating a verified address using the old array syntax for the `verify` key.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateVerifyArray()
    {
        VCR::insertCassette('addresses/createVerifyArray.yml');

        $addressData = Fixture::incorrectAddress();
        $addressData['verify'] = [true];

        $address = Address::create($addressData);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('417 MONTGOMERY ST FL 5', $address->street1);
        $this->assertEquals('Invalid secondary information(Apt/Suite#)', $address->verifications->zip4->errors[0]->message);
    }

    /**
     * Test retrieving an address.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('addresses/retrieve.yml');

        $address = Address::create(Fixture::caAddress1());

        $retrievedAddress = Address::retrieve($address->id);

        $this->assertInstanceOf('\EasyPost\Address', $retrievedAddress);
        $this->assertEquals($address, $retrievedAddress);
    }

    /**
     * Test retrieving all addresses.
     */
    public function testAll()
    {
        VCR::insertCassette('addresses/all.yml');

        $addresses = Address::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $addressesArray = $addresses['addresses'];

        $this->assertLessThanOrEqual($addressesArray, Fixture::pageSize());
        $this->assertNotNull($addresses['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Address', $addressesArray);
    }

    /**
     * Test creating a verified address.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateAndVerify()
    {
        VCR::insertCassette('addresses/createAndVerify.yml');

        $addressData = Fixture::incorrectAddress();

        $address = Address::create_and_verify($addressData);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('417 MONTGOMERY ST FL 5', $address->street1);
    }

    /**
     * Test we can verify an already created address.
     */
    public function testVerify()
    {
        VCR::insertCassette('addresses/verify.yml');

        $address = Address::create(Fixture::caAddress1());

        $address->verify();

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 Townsend St', $address->street1);
    }
}
