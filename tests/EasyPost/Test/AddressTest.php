<?php

namespace EasyPost\Test;

use EasyPost\Address;
use EasyPost\EasyPost;

EasyPost::setApiKey('cueqNZUb3ldeWTNX7MU3Mel8UXtaAMUi');

class AddressTest extends \PHPUnit_Framework_TestCase
{

    // TODO: set up tests for exceptions and error codes

    public function testCreate()
    {
        $address_params = array("street1" => "388 Townsend St",
                                "street2" => "Apt 20",
                                "city"    => "San Francisco",
                                "state"   => "CA",
                                "zip"     => "94107");
        $address = Address::create($address_params);
        $this->assertInstanceOf('EasyPost_Address', $address);
        $this->assertInternalType('string', $address->id);
        $this->assertStringMatchesFormat("adr_%s", $address->id);
        $this->assertNull($address->name);

        return $address;
    }

    /**
     * @depends testCreate
     */
    public function testRetrieve(Address $address)
    {
        $retrieved_address = Address::retrieve($address->id);

        $this->assertInstanceOf('EasyPost_Address', $retrieved_address);
        $this->assertEquals($retrieved_address->id, $address->id);
        $this->assertEquals($retrieved_address, $address);

        return $retrieved_address;
    }

    /**
     * @depends testRetrieve
     */
    public function testSave(Address $address)
    {
        $address->street2 = "Apt 30";
        $address->save();

        print_r($address);
    }


    /**
     * @depends testRetrieve
     */
    public function testAll(Address $address)
    {
        $all = Address::all();

        $address_in_all = false;
        for ($_i = 0, $_k = count($all); $_i < $_k; $_i++) {
            if ($all[$_i]->id === $address->id) {
                $address_in_all = true;
            }
        }

        $this->assertTrue($address_in_all);
    }
}
