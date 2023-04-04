<?php

namespace EasyPost\Test;

use EasyPost\Batch;
use EasyPost\EasyPostClient;

class BatchTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a Batch.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('batches/create.yml');

        $batch = self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        $this->assertInstanceOf(Batch::class, $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertNotNull($batch->shipments);
    }

    /**
     * Test retrieving a Batch.
     */
    public function testRetrieve()
    {
        TestUtil::setupCassette('batches/retrieve.yml');

        $batch = self::$client->batch->create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        $retrievedBatch = self::$client->batch->retrieve($batch->id);

        $this->assertInstanceOf(Batch::class, $retrievedBatch);
        $this->assertEquals($batch->id, $retrievedBatch->id);
    }

    /**
     * Test retrieving all batches.
     */
    public function testAll()
    {
        TestUtil::setupCassette('batches/all.yml');

        $batches = self::$client->batch->all([
            'page_size' => Fixture::pageSize(),
        ]);

        $batchesArray = $batches['batches'];

        $this->assertLessThanOrEqual($batchesArray, Fixture::pageSize());
        $this->assertNotNull($batches['has_more']);
        $this->assertContainsOnlyInstancesOf(Batch::class, $batchesArray);
    }

    /**
     * Test creating and buying a Batch in a single call.
     */
    public function testCreateAndBuy()
    {
        TestUtil::setupCassette('batches/createAndBuy.yml');

        $batch = self::$client->batch->createAndBuy([
            Fixture::oneCallBuyShipment(),
            Fixture::oneCallBuyShipment(),
        ]);

        $this->assertInstanceOf(Batch::class, $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertEquals(2, $batch->num_shipments);
    }

    /**
     * Test buying a batch.
     */
    public function testBuy()
    {
        TestUtil::setupCassette('batches/buy.yml');

        $shipmentData = Fixture::oneCallBuyShipment();

        $batch = self::$client->batch->create([
            'shipments' => [$shipmentData],
        ]);

        $boughtBatch = self::$client->batch->buy($batch->id);

        $this->assertInstanceOf(Batch::class, $boughtBatch);
        $this->assertEquals(1, $boughtBatch->num_shipments);
    }

    /**
     * Test creating a scanform for a batch.
     */
    public function testCreateScanForm()
    {
        $cassetteName = 'batches/createScanForm.yml';
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        TestUtil::setupCassette($cassetteName);

        $batch = self::$client->batch->create([
            'shipments' => [Fixture::oneCallBuyShipment()],
        ]);
        $boughtBatch = self::$client->batch->buy($batch->id);

        if ($testRequiresWait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

        $scanformBatch = self::$client->batch->createScanForm($boughtBatch->id);

        // We can't assert anything meaningful here because the scanform gets queued for generation
        // and may not be immediately available.
        $this->assertInstanceOf(Batch::class, $scanformBatch);
    }

    /**
     * Test adding and removing a shipment from a batch.
     */
    public function testAddRemoveShipment()
    {
        TestUtil::setupCassette('batches/addRemoveShipment.yml');

        $shipment = self::$client->shipment->create(Fixture::oneCallBuyShipment());

        $batch = self::$client->batch->create();

        $shipmentsBatch = self::$client->batch->addShipments(
            $batch->id,
            ['shipments' => [$shipment]]
        );
        $this->assertEquals(1, $shipmentsBatch->num_shipments);

        $nonShipmentsBatch = self::$client->batch->removeShipments(
            $batch->id,
            ['shipments' => [$shipment]]
        );
        $this->assertEquals(0, $nonShipmentsBatch->num_shipments);
    }

    /**
     * Test generating a label for a Batch.
     */
    public function testLabel()
    {
        $cassetteName = 'batches/label.yml';
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        TestUtil::setupCassette($cassetteName);

        $batch = self::$client->batch->create([
            'shipments' => [Fixture::oneCallBuyShipment()],
        ]);
        self::$client->batch->buy($batch->id);

        if ($testRequiresWait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

        $labelBatch = self::$client->batch->label(
            $batch->id,
            ['file_format' => 'ZPL']
        );

        // We can't assert anything meaningful here because the label gets queued for generation
        // and may not be immediately available.
        $this->assertInstanceOf(Batch::class, $labelBatch);
    }
}
