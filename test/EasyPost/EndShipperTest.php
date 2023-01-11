<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\EndShipper;

class EndShipperTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_TEST_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating an EndShipper.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('end_shipper/create.yml');

        $endShipper = self::$client->endShipper->create(Fixture::caAddress1());

        $this->assertInstanceOf(EndShipper::class, $endShipper);
        $this->assertStringMatchesFormat('es_%s', $endShipper->id);
        $this->assertEquals('388 TOWNSEND ST APT 20', $endShipper->street1);
    }

    /**
     * Test retrieving an EndShipper.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('end_shipper/retrieve.yml');

        $endShipper = self::$client->endShipper->create(Fixture::caAddress1());

        $retrievedEndShipper = self::$client->endShipper->retrieve($endShipper->id);

        $this->assertInstanceOf(EndShipper::class, $retrievedEndShipper);
        $this->assertEquals($endShipper->street1, $retrievedEndShipper->street1);
    }

    /**
     * Test retrieving all EndShippers.
     */
    public function testAll()
    {
        TestUtil::setupCassette('end_shipper/all.yml');

        $endShippers = self::$client->endShipper->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $endShipperArray = $endShippers['end_shippers'];

        $this->assertLessThanOrEqual($endShipperArray, Fixture::pageSize());
        $this->assertNotNull($endShippers['has_more']);
        $this->assertContainsOnlyInstancesOf(EndShipper::class, $endShipperArray);
    }

    /**
     * Test updating an EndShipper.
     */
    public function testUpdate()
    {
        TestUtil::setupCassette('end_shipper/update.yml');

        $endShipper = self::$client->endShipper->create(Fixture::caAddress1());

        // All caps because API will return all caps as part of verification.
        $newName = 'NEW NAME';

        $params = [
            'name' => $newName,
            'company' => 'EasyPost',
            'street1' => '388 Townsend St',
            'street2' => 'Apt 20',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94107',
            'country' => 'US',
            'phone' => '9999999999',
            'email' => 'test@example.com'
        ];

        $updatedEndShipper = self::$client->endShipper->update($endShipper->id, $params);

        $this->assertInstanceOf(EndShipper::class, $updatedEndShipper);
        $this->assertStringMatchesFormat('es_%s', $updatedEndShipper->id);
        $this->assertEquals($newName, $updatedEndShipper->name);
    }
}
