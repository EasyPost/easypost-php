<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;

class CarrierMetadataTest extends \PHPUnit\Framework\TestCase
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

        $carrierMetadata = self::$client->carrierMetadata->retrieve();

        // Assert we get multiple carriers
        $uspsFound = false;
        $fedexFound = false;
        foreach ($carrierMetadata as $carrier) {
            if ($carrier->name == 'usps') {
                $uspsFound = true;
            }
            if ($carrier->name == 'fedex') {
                $fedexFound = true;
            }
            if ($uspsFound && $fedexFound) {
                break;
            }
        }

        $this->assertTrue($uspsFound);
        $this->assertTrue($fedexFound);
    }

    /**
     * Tests that we can retrieve metadata based on the filters provided.
     */
    public function testRetrieveCarrierMetadataWithFilters()
    {
        TestUtil::setupCassette('carrier_metadata/retrieveCarrierMetadataWithFilters.yml');

        $carrierMetadata = self::$client->carrierMetadata->retrieve(
            ['usps'],
            ['service_levels', 'predefined_packages'],
        );

        // Assert we get the single carrier we asked for and only the types we asked for
        $uspsFound = false;
        foreach ($carrierMetadata as $carrier) {
            if ($carrier->name == 'usps') {
                $uspsFound = true;
                break;
            }
        }

        $this->assertTrue($uspsFound);
        $this->assertEquals(1, count($carrierMetadata));
        $this->assertNotNull($carrierMetadata[0]['service_levels']);
        $this->assertNotNull($carrierMetadata[0]['predefined_packages']);
        $this->assertNull($carrierMetadata[0]['supported_features']);
    }
}
