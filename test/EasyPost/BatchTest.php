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
     * @return void
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
    }

    /**
     * Test retrieving a Batch.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('batches/retrieve.yml');

        $batch = Batch::create([
            'shipments' => [Fixture::basic_shipment()],
        ]);

        $retrieved_batch = Batch::retrieve($batch->id);

        $this->assertInstanceOf('\EasyPost\Batch', $retrieved_batch);
        $this->assertEquals($batch->id, $retrieved_batch->id);
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
        $this->assertNotNull($batches['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\Batch', $batches_array);
    }

    /**
     * Test creating and buying a Batch in a single call.
     *
     * @return void
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
        $this->assertEquals(2, $batch->num_shipments);
    }

    /**
     * Test buying a batch.
     *
     * @return void
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
        $this->assertEquals(1, $batch->num_shipments);

        // Return so other tests can reuse this object
        return $batch;
    }

    /**
     * Test creating a scanform for a batch.
     *
     * @return void
     */
    public function testCreateScanForm()
    {
        $cassette_name = 'batches/createScanForm.yml';
        $test_requires_wait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassette_name") === false : false;

        VCR::insertCassette($cassette_name);

        $batch = Batch::create([
            'shipments' => [Fixture::one_call_buy_shipment()],
        ]);
        $batch->buy();

        if ($test_requires_wait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

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
        $this->assertEquals(1, $batch->num_shipments);

        $batch->remove_shipments([
            'shipments' => [$shipment]
        ]);
        $this->assertEquals(0, $batch->num_shipments);
    }

    /**
     * Test generating a label for a Batch.
     *
     * @return void
     */
    public function testLabel()
    {
        $cassette_name = 'batches/label.yml';
        $test_requires_wait = true ? file_exists(dirname(__DIR__, 1) . "/cassettes/$cassette_name") === false : false;

        VCR::insertCassette($cassette_name);

        $batch = Batch::create([
            'shipments' => [Fixture::one_call_buy_shipment()],
        ]);
        $batch->buy();

        if ($test_requires_wait === true) {
            sleep(5); // Wait enough time for the batch to process buying the shipment
        }

        $batch->label([
            'file_format' => 'ZPL',
        ]);

        // We can't assert anything meaningful here because the label gets queued for generation and may not be immediately available
        $this->assertInstanceOf('\EasyPost\Batch', $batch);
    }
}
