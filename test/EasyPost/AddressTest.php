<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Address;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating an address
     *
     * @return Address
     */
    public function testCreate()
    {
        VCR::insertCassette('addresses/create.yml');

        $address = Address::create(Fixture::basic_address());

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '388 Townsend St');

        // Return so other tests can reuse this object
        return $address;
    }

    /**
     * Test creating an address with verify_strict param
     *
     * @return Address
     */
    public function testCreateVerifyStrict()
    {
        VCR::insertCassette('addresses/createVerifyStrict.yml');

        $address_data = Fixture::basic_address();
        $address_data['verify_strict'] = [true];

        $address = Address::create($address_data);

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '388 TOWNSEND ST APT 20');

        // Return so other tests can reuse this object
        return $address;
    }

    /**
     * Test retrieving an address
     *
     * @param Address $address
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Address $address)
    {
        VCR::insertCassette('addresses/retrieve.yml');

        $retrieved_address = Address::retrieve($address->id);

        $this->assertInstanceOf('\EasyPost\Address', $retrieved_address);
        $this->assertEquals($retrieved_address, $address);
    }

    /**
     * Test retrieving all addresses
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('addresses/all.yml');

        $addresses = Address::all([
            'page_size' => Fixture::page_size(),
        ]);

        $addresses_array = $addresses['addresses'];

        $this->assertLessThanOrEqual($addresses_array, Fixture::page_size());
        foreach ($addresses_array as $address) {
            $this->assertInstanceOf('\EasyPost\Address', $address);
        }
    }

    /**
     * Test creating a verified address
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified
     *
     * @return void
     */
    public function testCreateVerify()
    {
        VCR::insertCassette('addresses/createVerify.yml');

        $address = Address::create(Fixture::incorrect_address_to_verify());

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '417 MONTGOMERY ST STE 500');
    }

    /**
     * Test creating a verified address
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified
     *
     * @return void
     */
    public function testCreateAndVerify()
    {
        VCR::insertCassette('addresses/createAndVerify.yml');

        $address = Address::create_and_verify(Fixture::incorrect_address_to_verify());

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '417 MONTGOMERY ST STE 500');
    }

    /**
     * Test we can verify an already created address
     *
     * @param Address $address
     * @return void
     * @depends testCreate
     */
    public function testVerify(Address $address)
    {
        VCR::insertCassette('addresses/verify.yml');

        $address->verify(Fixture::incorrect_address_to_verify());

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '388 Townsend St');
    }
}
