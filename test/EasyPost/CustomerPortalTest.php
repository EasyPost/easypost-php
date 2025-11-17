<?php

namespace EasyPost\Test;

use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class CustomerPortalTest extends TestCase
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
    public function testCreateAccountLink(): void
    {
        TestUtil::setupCassette('customer_portal/createAccountLink.yml');

        $user = self::$client->user->allChildren()['children'][0];

        $accountLink = self::$client->customerPortal->createAccountLink(
            [
                'session_type' => "account_onboarding",
                'user_id' => $user->id,
                'refresh_url' => "https://example.com/refresh",
                'return_url' => "https://example.com/return",
            ]
        );

        $this->assertEquals('CustomerPortalAccountLink', $accountLink->object);
    }
}
