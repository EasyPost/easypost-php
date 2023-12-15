<?php

namespace EasyPost;

use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Test\Fixture;
use EasyPost\Test\TestUtil;

class BetaUserTest extends \PHPUnit\Framework\TestCase
{
    private static EasyPostClient $client;

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
     * Test retrieving all child users.
     */
    public function testAllChildren(): void
    {
        TestUtil::setupCassette('beta/user/allChildren.yml');

        $children = self::$client->betaUser->allChildren([
            'page_size' => Fixture::pageSize(),
        ]);

        $userArray = $children['children'];

        $this->assertLessThanOrEqual($userArray, Fixture::pageSize());
        $this->assertNotNull($children['has_more']);
        $this->assertContainsOnlyInstancesOf(User::class, $userArray);
    }

    /**
     * Test retrieving next page.
     */
    public function testGetNextPageOfChildren(): void
    {
        TestUtil::setupCassette('beta/user/getNextPageOfChildren.yml');

        try {
            $children = self::$client->betaUser->allChildren([
                'page_size' => Fixture::pageSize(),
            ]);
            $nextPage = self::$client->betaUser->getNextPageOfChildren($children, Fixture::pageSize());

            $firstIdOfFirstPage = $children['children'][0]->id;
            $secondIdOfSecondPage = $nextPage['children'][0]->id;

            $this->assertNotEquals($firstIdOfFirstPage, $secondIdOfSecondPage);
        } catch (EndOfPaginationException $error) {
            // There's no second page, that's not a failure
            $this->assertTrue(true);
        } catch (Exception $error) {
            throw $error;
        }
    }
}
