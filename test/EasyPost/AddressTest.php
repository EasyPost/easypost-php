<?php

namespace EasyPost\Test;

use EasyPost\Address;

class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests('EASYPOST_TEST_API_KEY');
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating an address.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('addresses/create.yml');

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
        TestUtil::setupCassette('addresses/createVerify.yml');

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
        TestUtil::setupCassette('addresses/createVerifyStrict.yml');

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
        TestUtil::setupCassette('addresses/createVerifyArray.yml');

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
        TestUtil::setupCassette('addresses/retrieve.yml');

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
        TestUtil::setupCassette('addresses/all.yml');

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
        TestUtil::setupCassette('addresses/createAndVerify.yml');

        $addressData = Fixture::incorrectAddress();

        $address = Address::createAndVerify($addressData);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('417 MONTGOMERY ST FL 5', $address->street1);
    }

    /**
     * Test we can verify an already created address.
     */
    public function testVerify()
    {
        TestUtil::setupCassette('addresses/verify.yml');

        $address = Address::create(Fixture::caAddress1());

        $address->verify();

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 Townsend St', $address->street1);
    }

    /**
     * Test we throw an error for an invalid address verification.
     */
    public function testVerifyInvalid()
    {
        TestUtil::setupCassette('addresses/verifyInvalid.yml');

        try {
            $address = Address::create(['street1' => 'invalid']);
            $address->verify();
        } catch (\EasyPost\Error $error) {
            $this->assertEquals('Unable to verify address.', $error->getMessage());
        }
    }
}
