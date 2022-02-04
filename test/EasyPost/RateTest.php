<?php

namespace EasyPost\Test;

use VCR\VCR;
use EasyPost\Rate;
use EasyPost\EasyPost;
use EasyPost\Test\Fixture;

EasyPost::setApiKey(getenv('EASYPOST_TEST_API_KEY'));

class RateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Set up VCR before running tests in this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        VCR::turnOn();
    }

    /**
     * Spin down VCR after running tests.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test retrieving a rate.
     *
     * @return void
     */
    public function testRetrieve()
    {
        VCR::insertCassette('rates/retrieve.yml');

        $rate = Rate::retrieve(Fixture::rate_id());

        $this->assertInstanceOf('\EasyPost\Rate', $rate);
        $this->assertStringMatchesFormat('rate_%s', $rate->id);
    }
}
