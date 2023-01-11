<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Parcel;

class ParcelTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Parcel.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('parcels/create.yml');

        $parcel = self::$client->parcel->create(Fixture::basicParcel());

        $this->assertInstanceOf(Parcel::class, $parcel);
        $this->assertStringMatchesFormat('prcl_%s', $parcel->id);
        $this->assertEquals(15.4, $parcel->weight);
    }

    /**
     * Test retrieving a Parcel.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('parcels/retrieve.yml');

        $parcel = self::$client->parcel->create(Fixture::basicParcel());

        $retrievedParcel = self::$client->parcel->retrieve($parcel->id);

        $this->assertInstanceOf(Parcel::class, $retrievedParcel);
        $this->assertEquals($parcel, $retrievedParcel);
    }
}
