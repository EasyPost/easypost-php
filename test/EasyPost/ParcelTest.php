<?php

namespace EasyPost\Test;

use EasyPost\Parcel;

class ParcelTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Parcel.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('parcels/create.yml');

        $parcel = Parcel::create(Fixture::basicParcel());

        $this->assertInstanceOf('\EasyPost\Parcel', $parcel);
        $this->assertStringMatchesFormat('prcl_%s', $parcel->id);
        $this->assertEquals(15.4, $parcel->weight);
    }

    /**
     * Test retrieving a Parcel.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('parcels/retrieve.yml');

        $parcel = Parcel::create(Fixture::basicParcel());

        $retrievedParcel = Parcel::retrieve($parcel->id);

        $this->assertInstanceOf('\EasyPost\Parcel', $retrievedParcel);
        $this->assertEquals($parcel, $retrievedParcel);
    }
}
