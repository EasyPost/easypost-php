<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Http\Requestor;

class ErrorTest extends \PHPUnit\Framework\TestCase
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
     * Test creating a bad shipment and retrieving errors.
     */
    public function testError()
    {
        TestUtil::setupCassette('errors/errors.yml');

        // Create a bad shipment so we can work with errors
        try {
            self::$client->shipment->create();
        } catch (ApiException $error) {
            $this->assertEquals(422, $error->getHttpStatus());
            $this->assertEquals('PARAMETER.REQUIRED', $error->code);
            $this->assertEquals('Missing required parameter.', $error->getMessage());
            $this->assertEquals(['field' => 'shipment', 'message' => 'cannot be blank'], $error->errors[0]);
            $this->assertEquals('{"error":{"code":"PARAMETER.REQUIRED","message":"Missing required parameter.","errors":[{"field":"shipment","message":"cannot be blank"}]}}', $error->getHttpBody()); // phpcs:ignore

            // We check that the pretty printed output is the same here, leave the odd formatting as
            // it is here and do not auto-format the next few lines.
            $error->prettyPrint();
            $this->expectOutputString('PARAMETER.REQUIRED (422): Missing required parameter.
Field errors:
  field: shipment
  message: cannot be blank

');
        }
    }

    /**
     * Test error deserialization with an array of error.message
     */
    public function testErrorMessageArray()
    {
        $errorResponse = json_decode('{
            "error": {
                "code": "UNPROCESSABLE_ENTITY",
                "message": ["Bad format", "Bad format 2"],
                "errors": []
            }
        }', true);

        try {
            Requestor::handleApiError(null, 404, $errorResponse);
        } catch (EasyPostException $error) {
            $this->assertEquals('Bad format, Bad format 2', $error->getMessage());
        }
    }

    /**
     * Test error deserialization with an Array Map of error.message
     */
    public function testErrorMessageMap()
    {
        $errorResponse = json_decode('{
            "error": {
                "code": "UNPROCESSABLE_ENTITY",
                "message": {
                    "errors": [
                        "Bad format", "Bad format 2"
                    ]
                },
                "errors": []
            }
        }', true);

        try {
            Requestor::handleApiError(null, 404, $errorResponse);
        } catch (EasyPostException $error) {
            $this->assertEquals('Bad format, Bad format 2', $error->getMessage());
        }
    }

    /**
     * Test error deserialization with an really bad format of error.message
     */
    public function testErrorMessageBadFormat()
    {
        $errorResponse = json_decode('{
            "error": {
                "code": "UNPROCESSABLE_ENTITY",
                "message": {
                    "errors": [
                        "Bad format",
                        "Bad format 2"
                    ],
                    "bad_data": [
                        {
                            "first_message": "Bad format 3",
                            "second_message": "Bad format 4",
                            "thrid_message": "Bad format 5"
                        }
                    ]
                },
                "errors": []
            }
        }', true);


        try {
            Requestor::handleApiError(null, 404, $errorResponse);
        } catch (EasyPostException $error) {
            $this->assertEquals(
                'Bad format, Bad format 2, Bad format 3, Bad format 4, Bad format 5',
                $error->getMessage()
            );
        }
    }
}
