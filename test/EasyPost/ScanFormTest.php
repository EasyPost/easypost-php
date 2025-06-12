<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\ScanForm;
use Exception;
use PHPUnit\Framework\TestCase;

class ScanFormTest extends TestCase
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
     * Test creating a scanForm.
     */
    public function testCreate(): void
    {
        TestUtil::setupCassette('scanForms/create.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $scanForm = self::$client->scanForm->create([
            'shipments' => [$shipment],
        ]);

        $this->assertInstanceOf(ScanForm::class, $scanForm);
        $this->assertStringMatchesFormat('sf_%s', $scanForm->id);
    }

    /**
     * Test retrieving a scanForm.
     */
    public function testRetrieve(): void
    {
        TestUtil::setupCassette('scanForms/retrieve.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $scanForm = self::$client->scanForm->create([
            'shipments' => [$shipment],
        ]);

        $retrievedScanform = self::$client->scanForm->retrieve($scanForm->id);

        $this->assertInstanceOf(ScanForm::class, $retrievedScanform);
        $this->assertEquals($scanForm, $retrievedScanform);
    }

    /**
     * Test retrieving all scanForms.
     */
    public function testAll(): void
    {
        TestUtil::setupCassette('scanForms/all.yml');

        $scanForms = self::$client->scanForm->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $scanformsArray = $scanForms['scan_forms'];

        $this->assertLessThanOrEqual($scanformsArray, Fixture::pageSize());
        $this->assertNotNull($scanForms['has_more']);
        $this->assertContainsOnlyInstancesOf(ScanForm::class, $scanformsArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPage(): void
    {
        TestUtil::setupCassette('scanForms/getNextPage.yml');

        try {
            $scanforms = self::$client->scanForm->all([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->scanForm->getNextPage($scanforms, Fixture::pageSize());

            $firstIdOfFirstPage = $scanforms['scan_forms'][0]->id;
            $secondIdOfSecondPage = $nextPage['scan_forms'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->expectNotToPerformAssertions();
        } catch (Exception $error) {
            throw $error;
        }
    }
}
