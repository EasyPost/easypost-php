<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\Error;
use EasyPost\Shipment;
use VCR\VCR;

class ErrorTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a bad shipment and retrieving errors.
     */
    public function testError()
    {
        VCR::insertCassette('errors/errors.yml');

        // Create a bad shipment so we can work with errors
        try {
            Shipment::create();
        } catch (Error $error) {
            $this->assertEquals(422, $error->getHttpStatus());
            $this->assertEquals('SHIPMENT.INVALID_PARAMS', $error->ecode);
            $this->assertEquals('Unable to create shipment, one or more parameters were invalid.', $error->getMessage());
            $this->assertEquals(["to_address" => "Required and missing."], $error->errors[0]);
            $this->assertEquals(["from_address" => "Required and missing."], $error->errors[1]);
            $this->assertEquals('{"error":{"code":"SHIPMENT.INVALID_PARAMS","message":"Unable to create shipment, one or more parameters were invalid.","errors":[{"to_address":"Required and missing."},{"from_address":"Required and missing."}]}}', $error->getHttpBody());

            // We check that the pretty printed output is the same here, leave the odd formatting as it is here and do not auto-format the next few lines
            $error->prettyPrint();
            $this->expectOutputString('SHIPMENT.INVALID_PARAMS (422): Unable to create shipment, one or more parameters were invalid.
Field errors:
  to_address: Required and missing.

  from_address: Required and missing.

');
        }
    }
}
