<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Batch;
use EasyPost\Shipment;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

class BatchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test creating a Batch.
     *
     * @return Batch
     */
    public function testCreate()
    {
        VCR::insertCassette('batches/create.yml');

        $batch = Batch::create([
            'shipments' => [Fixture::basic_shipment()],
        ]);

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertNotNull($batch->shipments);

        // Return so other tests can reuse this object
        return $batch;
    }

    /**
     * Test retrieving a Batch.
     *
     * @param Batch $batch
     * @return void
     * @depends testCreate
     */
    public function testRetrieve(Batch $batch)
    {
        VCR::insertCassette('batches/retrieve.yml');

        $retrieved_batch = Batch::retrieve($batch->id);

        $this->assertInstanceOf('\EasyPost\Batch', $retrieved_batch);
        $this->assertEquals($retrieved_batch->id, $batch->id);
    }

    /**
     * Test retrieving all batches.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('batches/all.yml');

        $batches = Batch::all([
            'page_size' => Fixture::page_size(),
        ]);

        $batches_array = $batches['batches'];

        $this->assertLessThanOrEqual($batches_array, Fixture::page_size());
        foreach ($batches_array as $batch) {
            $this->assertInstanceOf('\EasyPost\Batch', $batch);
        }
    }

    /**
     * Test creating and buying a Batch in a single call.
     *
     * @return Batch
     */
    public function testCreateAndBuy()
    {
        VCR::insertCassette('batches/createAndBuy.yml');

        $batch = Batch::create_and_buy([
            Fixture::one_call_buy_shipment(),
            Fixture::one_call_buy_shipment(),
        ]);

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertStringMatchesFormat('batch_%s', $batch->id);
        $this->assertEquals($batch->num_shipments, 2);
    }

    /**
     * Test buying a batch.
     *
     * @return Batch
     */
    public function testBuy()
    {
        VCR::insertCassette('batches/buy.yml');

        $shipment_data = Fixture::one_call_buy_shipment();

        $batch = Batch::create([
            'shipments' => [$shipment_data],
        ]);

        $batch->buy();

        $this->assertInstanceOf('\EasyPost\Batch', $batch);
        $this->assertEquals($batch->num_shipments, 1);

        // Return so other tests can reuse this object
        return $batch;
    }

    /**
     * Test creating a scanform for a batch.
     *
     * @param Batch
     * @return void
     * @depends testBuy
     */
    public function testCreateScanForm(Batch $batch)
    {
        VCR::insertCassette('batches/createScanForm.yml');

        $batch->create_scan_form();

        // We can't assert anything meaningful here because the scanform gets queued for generation and may not be immediately available
        $this->assertInstanceOf('\EasyPost\Batch', $batch);
    }

    /**
     * Test adding and removing a shipment from a batch.
     *
     * @return void
     */
    public function testAddRemoveShipment()
    {
        VCR::insertCassette('batches/addRemoveShipment.yml');

        $shipment = Shipment::create(Fixture::one_call_buy_shipment());

        $batch = Batch::create();

        $batch->add_shipments([
            'shipments' => [$shipment]
        ]);
        $this->assertEquals($batch->num_shipments, 1);

        $batch->remove_shipments([
            'shipments' => [$shipment]
        ]);
        $this->assertEquals($batch->num_shipments, 0);
    }

    /**
     * Test generating a label for a Batch.
     *
     * @param Batch $batch
     * @return void
     * @depends testBuy
     */
    public function testLabel(Batch $batch)
    {
        VCR::insertCassette('batches/label.yml');

        $batch->label([
            'file_format' => 'ZPL',
        ]);

        // We can't assert anything meaningful here because the label gets queued for generation and may not be immediately available
        $this->assertInstanceOf('\EasyPost\Batch', $batch);
    }
}
