<?php

namespace EasyPost\Test;

use EasyPost\Batch;
use EasyPost\EasyPost;
use EasyPost\Shipment;
use EasyPost\Test\Fixture;
use VCR\VCR;

class BatchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a Batch.
     */
    public function testCreate()
    {
        VCR::insertCassette('batches/create.yml');

        $batch = Batch::create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertNotNull($batch->shipments);
    }

    /**
     * Test retrieving a Batch.
     */
    public function testRetrieve()
    {
        VCR::insertCassette('batches/retrieve.yml');

        $batch = Batch::create([
            'shipments' => [Fixture::basicShipment()],
        ]);

        $retrievedBatch = Batch::retrieve($batch->id);

        $this->assertInstanceOf('\EasyPost\Batch', $retrievedBatch);
        $this->assertEquals($batch->id, $retrievedBatch->id);
    }

    /**
     * Test retrieving all batches.
     */
    public function testAll()
    {
        VCR::insertCassette('batches/all.yml');

        $batches = Batch::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $batchesArray = $batches['batches'];

        $this->assertLessThanOrEqual($batchesArray, Fixture::pageSize());
        $this->assertNotNull($batches['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Batch', $batchesArray);
    }

    /**
     * Test creating and buying a Batch in a single call.
     */
    public function testCreateAndBuy()
    {
        VCR::insertCassette('batches/createAndBuy.yml');

        $batch = Batch::create_and_buy([
            Fixture::oneCallBuyShipment(),
            Fixture::oneCallBuyShipment(),
        ]);

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertEquals(2, $batch->num_shipments);
    }

    /**
     * Test buying a batch.
     */
    public function testBuy()
    {
        VCR::insertCassette('batches/buy.yml');

        $shipmentData = Fixture::oneCallBuyShipment();

        $batch = Batch::create([
            'shipments' => [$shipmentData],
        ]);

        $batch->buy();

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertEquals(1, $batch->num_shipments);

        // Return so other tests can reuse this object
        return $batch;
    }

    /**
     * Test creating a scanform for a batch.
     */
    public function testCreateScanForm()
    {
        $cassetteName = 'batches/createScanForm.yml';
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        VCR::insertCassette($cassetteName);

        $batch = Batch::create([
            'shipments' => [Fixture::oneCallBuyShipment()],
        ]);
        $batch->buy();

        if ($testRequiresWait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

        $batch->create_scan_form();

        // We can't assert anything meaningful here because the scanform gets queued for generation and may not be immediately available
        $this->assertInstanceOf('\EasyPost\Batch', $batch);
    }

    /**
     * Test adding and removing a shipment from a batch.
     */
    public function testAddRemoveShipment()
    {
        VCR::insertCassette('batches/addRemoveShipment.yml');

        $shipment = Shipment::create(Fixture::oneCallBuyShipment());

        $batch = Batch::create();

        $batch->add_shipments([
            'shipments' => [$shipment]
        ]);
        $this->assertEquals(1, $batch->num_shipments);

        $batch->remove_shipments([
            'shipments' => [$shipment]
        ]);
        $this->assertEquals(0, $batch->num_shipments);
    }

    /**
     * Test generating a label for a Batch.
     */
    public function testLabel()
    {
        $cassetteName = 'batches/label.yml';
        $testRequiresWait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassetteName") === false : false;

        VCR::insertCassette($cassetteName);

        $batch = Batch::create([
            'shipments' => [Fixture::oneCallBuyShipment()],
        ]);
        $batch->buy();

        if ($testRequiresWait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

        $batch->label([
            'file_format' => 'ZPL',
        ]);

        // We can't assert anything meaningful here because the label gets queued for generation and may not be immediately available
        $this->assertInstanceOf('\EasyPost\Batch', $batch);
    }
}
