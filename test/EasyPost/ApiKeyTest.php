<?php

namespace EasyPost\Test;

use EasyPost\ApiKey;
use EasyPost\EasyPostClient;
use PHPUnit\Framework\TestCase;

class ApiKeyTest extends TestCase
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
     * Test retrieving the authenticated user's API keys.
     */
    public function testAuthenticatedUserApiKeys(): void
    {
        TestUtil::setupCassette('apiKeys/authenticatedUserApiKeys.yml');

        $user = self::$client->user->retrieveMe();

        $apiKeys = self::$client->apiKeys->retrieveApiKeysForUser($user->id);

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys);
    }

    /**
     * Test retrieving the authenticated user's API keys.
     */
    public function testRetrieveChildUserApiKeys(): void
    {
        TestUtil::setupCassette('apiKeys/retrieveChildUserApiKeys.yml');

        $user = self::$client->user->create([
            'name' => 'Test User',
        ]);
        $childUser = self::$client->user->retrieve($user->id);

        $apiKeys = self::$client->apiKeys->retrieveApiKeysForUser($childUser->id);

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys);

        // Delete the user once done so we don't pollute with hundreds of child users
        self::$client->user->delete($childUser->id);
    }

    /**
     * Test retrieving all API keys.
     */
    public function testAllApiKeys(): void
    {
        TestUtil::setupCassette('apiKeys/allApiKeys.yml');

        $apiKeys = self::$client->apiKeys->all();

        $this->assertContainsOnlyInstancesOf(ApiKey::class, $apiKeys['keys']);
        foreach ($apiKeys['children'] as $child) {
            $this->assertContainsOnlyInstancesOf(ApiKey::class, $child['keys']);
        }
    }

    /**
     * Test creating an API key for a child user.
     */
    public function testApiKeyLifecycle(): void
    {
        TestUtil::setupCassette('apiKeys/lifecycle.yml');

        // Create an API key
        $referralClient = new EasyPostClient((string)getenv('REFERRAL_CUSTOMER_PROD_API_KEY'));
        $apiKey = $referralClient->apiKeys->create('production');
        $this->assertInstanceOf(ApiKey::class, $apiKey);
        $this->assertStringMatchesFormat('ak_%s', $apiKey->id);
        $this->assertEquals('production', $apiKey->mode);

        // Disable the API key
        $apiKey = $referralClient->apiKeys->disable($apiKey->id);
        $this->assertFalse($apiKey->active);

        // Enable the API key
        $apiKey = $referralClient->apiKeys->enable($apiKey->id);
        $this->assertTrue($apiKey->active);

        // Delete the API key
        $referralClient->apiKeys->delete($apiKey->id);
    }
}
