<?php

namespace EasyPost\Test;

use EasyPost\ApiKey;
use EasyPost\EasyPostClient;

class ApiKeyTest extends \PHPUnit\Framework\TestCase
{
    private static $client;

    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests();
        self::$client = new EasyPostClient(getenv('EASYPOST_PROD_API_KEY'));
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test retrieving all API keys.
     */
    public function testAllApiKeys()
    {
        TestUtil::setupCassette('apiKeys/allApiKeys.yml');

        $apiKeys = self::$client->apiKeys->all();

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys['keys']);
        foreach ($apiKeys['children'] as $child) {
            $this->assertContainsOnlyInstancesOf(ApiKey::class, $child['keys']);
        }
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testAuthenticatedUserApiKeys()
    {
        TestUtil::setupCassette('apiKeys/authenticatedUserApiKeys.yml');

        $user = self::$client->user->retrieveMe();

        $apiKeys = self::$client->apiKeys->retrieveApiKeysForUser($user->id);

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys);
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testChildUserApiKeys()
    {
        TestUtil::setupCassette('apiKeys/childUserApiKeys.yml');

        $user = self::$client->user->create([
            'name' => 'Test User',
        ]);
        $childUser = self::$client->user->retrieve($user->id);

        $apiKeys = self::$client->apiKeys->retrieveApiKeysForUser($childUser->id);

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys);

        // Delete the user once done so we don't pollute with hundreds of child users
        self::$client->user->delete($childUser->id);
    }
}
