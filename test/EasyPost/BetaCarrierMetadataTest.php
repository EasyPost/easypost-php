<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;

class BetaCarrierMetadataTest extends \PHPUnit\Framework\TestCase
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
     * Tests that we can retrieve all carriers and all metadata from the API when no params are provided.
     */
    public function testRetrieveCarrierMetadata()
    {
        TestUtil::setupCassette('carrier_metadata/retrieveCarrierMetadata.yml');

        $carrierMetadata = self::$client->carrierMetadata->retrieveCarrierMetadata();

        // Assert we get multiple carriers
        $usps_found = false;
        $fedex_found = false;
        foreach ($carrierMetadata as $carrier) {
            if ($carrier->name == "usps") {
                $usps_found = true;
            }
            if ($carrier->name == "fedex") {
                $fedex_found = true;
            }
            if ($usps_found && $fedex_found) {
                break;
            }
        }

        $this->assertTrue($usps_found);
        $this->assertTrue($fedex_found);
    }

    /**
     * Tests that we can retrieve metadata based on the filters provided.
     */
    public function testRetrieveCarrierMetadataWithFilters()
    {
        TestUtil::setupCassette('carrier_metadata/retrieveCarrierMetadataWithFilters.yml');

        $carrierMetadata = self::$client->carrierMetadata->retrieveCarrierMetadata(
            ['usps'],
            ['service_levels', 'predefined_packages'],
        );

        // Assert we get multiple carriers
        $usps_found = false;
        foreach ($carrierMetadata as $carrier) {
            if ($carrier->name == "usps") {
                $usps_found = true;
                break;
            }
        }

        $this->assertTrue($usps_found);
        $this->assertEquals(1, count($carrierMetadata));
        $this->assertNotNull($carrierMetadata[0]['service_levels']);
        $this->assertNotNull($carrierMetadata[0]['predefined_packages']);
        $this->assertNull($carrierMetadata[0]['supported_features']);
    }
}
