<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Address;
use EasyPost\EasyPost;

EasyPost::setApiKey(getenv('API_KEY'));

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
     * Test the creation of an address
     *
     * @return void
     */
    public function testCreate()
    {
        VCR::insertCassette('addresses/create.yml');

        $address = Address::create(array(
            "street1" => "388 Townsend St",
            "street2" => "Apt 20",
            "city"    => "San Francisco",
            "state"   => "CA",
            "zip"     => "94107",
        ));

        $this->assertInstanceOf('\EasyPost\Address', $address);
        $this->assertIsString($address->id);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals($address->street1, '388 Townsend St');

        // Return so the `retrieve` test can reuse this object
        return $address;
    }

    /**
     * Test the retrieval of an address
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
        $this->assertEquals($retrieved_address->id, $address->id);
        $this->assertEquals($retrieved_address, $address);
    }
}
