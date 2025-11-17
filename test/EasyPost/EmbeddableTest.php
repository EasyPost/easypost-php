<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class EmbeddableTest extends TestCase
{
    private static EasyPostClient $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient((string)getenv('EASYPOST_PROD_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating an account link
     */
    public function testCreateSession(): void
    {
        TestUtil::setupCassette('embeddables/createSession.yml');

        $user = self::$client->user->allChildren()['children'][0];

        $accountLink = self::$client->embeddable->createSession(
            [
                'origin_host' => "https://example.com",
                'user_id' => $user->id,
            ]
        );

        $this->assertEquals('EmbeddablesSession', $accountLink->object);
    }
}
