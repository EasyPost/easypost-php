<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Http\Requestor;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
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
     * Test creating a bad shipment and retrieving errors.
     */
    public function testError(): void
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
     * Tests that we assign properties of an error correctly when returned via the alternative format.
     * NOTE: Claims (among other things) uses the alternative errors format.
     */
    public function testErrorAlternativeFormat(): void
    {
        TestUtil::setupCassette('errors/errorsAlternativeFormat.yml');

        try {
            $claimData = Fixture::basicClaimData();
            $claimData['tracking_code'] = '123';  // Intentionally pass a bad tracking code

            self::$client->claim->create($claimData);
        } catch (ApiException $error) {
            $this->assertEquals(404, $error->getHttpStatus());
            $this->assertEquals('NOT_FOUND', $error->code);
            $this->assertEquals('The requested resource could not be found.', $error->getMessage());
            $this->assertEquals('No eligible insurance found with provided tracking code.', $error->errors[0]);
            $this->assertEquals('{"error":{"code":"NOT_FOUND","errors":["No eligible insurance found with provided tracking code."],"message":"The requested resource could not be found."}}', $error->getHttpBody()); // phpcs:ignore

            // We check that the pretty printed output is the same here, leave the odd formatting as
            // it is here and do not auto-format the next few lines.
            $error->prettyPrint();
            $this->expectOutputString('NOT_FOUND (404): The requested resource could not be found.
');
        }
    }

    /**
     * Test error deserialization with an array of error.message
     */
    public function testErrorMessageArray(): void
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
    public function testErrorMessageMap(): void
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
    public function testErrorMessageBadFormat(): void
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
