<?php

namespace EasyPost\Test;

use EasyPost\Referral;

class ReferralTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     */
    public static function setUpBeforeClass(): void
    {
        TestUtil::setupVcrTests('PARTNER_USER_PROD_API_KEY');
    }

    /**
     * Cleanup the testing environment once finished.
     */
    public static function tearDownAfterClass(): void
    {
        TestUtil::teardownVcrTests();
    }

    /**
     * Test creating a child user.
     */
    public function testCreate()
    {
        TestUtil::setupCassette('referrals/create.yml');

        $referral = Referral::create([
            'name' => 'Test Referral',
            'email' => 'test@test.com',
            'phone' => '8888888888'
        ]);

        $this->assertInstanceOf('\EasyPost\User', $referral);
        $this->assertStringMatchesFormat('user_%s', $referral->id);
        $this->assertEquals('Test Referral', $referral->name);
    }

    /**
     * Test retrieving a child user.
     */
    public function testAll()
    {
        TestUtil::setupCassette('referrals/all.yml');

        $referralUsers = Referral::all([
            'page_size' => Fixture::pageSize(),
        ]);

        $referralUsersArray = $referralUsers['referral_customers'];

        $this->assertLessThanOrEqual($referralUsersArray, Fixture::pageSize());
        $this->assertNotNull($referralUsers['has_more']);
        $this->assertContainsOnlyInstancesOf('\EasyPost\User', $referralUsersArray);
    }

    /**
     * Test retrieving the authenticated user.
     */
    public function testUpdateEmail()
    {
        TestUtil::setupCassette('referrals/updateEmail.yml');

        $referralUsers = Referral::all();

        $referralUser = Referral::updateEmail('email@example.com', $referralUsers['referral_customers'][0]['id']);

        $this->assertEquals($referralUser, true);
    }

    /**
     * Test that we can add a credit card to a referral user.
     *
     * This test requires a partner user's production API key via PARTNER_USER_PROD_API_KEY
     * as well as one of that user's referral's production API keys via REFERRAL_USER_PROD_API_KEY.
     */
    public function testReferralAddCreditCard()
    {
        TestUtil::setupCassette('referrals/addCreditCard.yml');

        $creditCard = Referral::addCreditCard(
            getenv('REFERRAL_USER_PROD_API_KEY'),
            Fixture::creditCardDetails()['number'],
            Fixture::creditCardDetails()['expiration_month'],
            Fixture::creditCardDetails()['expiration_year'],
            Fixture::creditCardDetails()['cvc']
        );

        $this->assertStringMatchesFormat('card_%s', $creditCard->id);
        $this->assertEquals('6170', $creditCard->last4);
    }
}
