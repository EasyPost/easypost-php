<?php

namespace EasyPost\Test;

use EasyPost\Address;
use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\EndOfPaginationException;
use Exception;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_TEST_API_KEY'));
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
    public function testCreate(): void
    {
        TestUtil::setupCassette('addresses/create.yml');

        $address = self::$client->address->create(Fixture::caAddress1());

        $this->assertInstanceOf(Address::class, $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 Townsend St', $address->street1);
    }

    /**
     * Test creating a verified address.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateVerify(): void
    {
        TestUtil::setupCassette('addresses/createVerify.yml');

        $addressData = Fixture::incorrectAddress();

        // Creating normally (without specifying "verify") will make the address, perform no verifications
        $address = self::$client->address->create($addressData);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEmpty($address->verifications);

        // Creating with verify would make the address and perform verifications
        $addressData['verify'] = true;
        $address = self::$client->address->create($addressData);

        $this->assertInstanceOf(Address::class, $address);

        // Delivery verification assertions
        $this->assertFalse($address->verifications->delivery->success);
        $this->assertEmpty($address->verifications->delivery->details);
        $this->assertEquals('E.ADDRESS.NOT_FOUND', $address->verifications->delivery->errors[0]->code);
        $this->assertEquals('address', $address->verifications->delivery->errors[0]->field);
        $this->assertNull($address->verifications->delivery->errors[0]->suggestion);
        $this->assertEquals('Address not found', $address->verifications->delivery->errors[0]->message);

        // Delivery verification assertions
        $this->assertFalse($address->verifications->zip4->success);
        $this->assertNull($address->verifications->zip4->details);
        $this->assertEquals('E.ADDRESS.NOT_FOUND', $address->verifications->zip4->errors[0]->code);
        $this->assertEquals('address', $address->verifications->zip4->errors[0]->field);
        $this->assertNull($address->verifications->zip4->errors[0]->suggestion);
        $this->assertEquals('Address not found', $address->verifications->zip4->errors[0]->message);
    }

    /**
     * Test creating an address with verify_strict param.
     */
    public function testCreateVerifyStrict(): void
    {
        TestUtil::setupCassette('addresses/createVerifyStrict.yml');

        $addressData = Fixture::caAddress1();
        $addressData['verify_strict'] = true;

        $address = self::$client->address->create($addressData);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $address->street1);
    }

    /**
     * Test creating a verified address using the old array syntax for the `verify` key.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateVerifyArray(): void
    {
        TestUtil::setupCassette('addresses/createVerifyArray.yml');

        $addressData = Fixture::incorrectAddress();

        // Creating normally (without specifying "verify") will make the address, perform no verifications
        $address = self::$client->address->create($addressData);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEmpty($address->verifications);

        // Creating with verify would make the address and perform verifications
        $addressData['verify'] = [true];
        $address = self::$client->address->create($addressData);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertNotNull($address->verifications->delivery);
        $this->assertNotNull($address->verifications->zip4);
    }

    /**
     * Test retrieving an address.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('addresses/retrieve.yml');

        $address = self::$client->address->create(Fixture::caAddress1());

        $retrievedAddress = self::$client->address->retrieve($address->id);

        $this->assertInstanceOf(Address::class, $retrievedAddress);
        $this->assertEquals($address, $retrievedAddress);
    }

    /**
     * Test retrieving all addresses.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('addresses/all.yml');

        $addresses = self::$client->address->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $addressesArray = $addresses['addresses'];

        $this->assertLessThanOrEqual($addressesArray, Fixture::pageSize());
        $this->assertNotNull($addresses['has_more']);
        $this->assertContainsOnlyInstancesOf(Address::class, $addressesArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('addresses/getNextPage.yml');

        try {
            $addresses = self::$client->address->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->address->getNextPage($addresses, Fixture::pageSize());

            $firstIdOfFirstPage = $addresses['addresses'][0]->id;
            $secondIdOfSecondPage = $nextPage['addresses'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Test creating a verified address.
     *
     * We purposefully pass in slightly incorrect data to get the corrected address back once verified.
     */
    public function testCreateAndVerify(): void
    {
        TestUtil::setupCassette('addresses/createAndVerify.yml');

        $addressData = Fixture::caAddress1();

        $address = self::$client->address->createAndVerify($addressData);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $address->street1);
    }

    /**
     * Test we can verify an already created address.
     */
    public function testVerify(): void
    {
        TestUtil::setupCassette('addresses/verify.yml');

        $address = self::$client->address->create(Fixture::caAddress1());

        self::$client->address->verify($address->id);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertStringMatchesFormat('adr_%s', $address->id);
        $this->assertEquals('388 Townsend St', $address->street1);
    }

    /**
     * Test we throw an error for an invalid address verification.
     */
    public function testVerifyInvalid(): void
    {
        TestUtil::setupCassette('addresses/verifyInvalid.yml');

        try {
            $address = self::$client->address->create(['street1' => 'invalid']);
            self::$client->address->verify($address->id);
        } catch (ApiException $error) {
            $this->assertEquals('Unable to verify address.', $error->getMessage());
        }
    }
}
