<?php

namespace EasyPost\Test;

use EasyPost\CreditCard;
use EasyPost\EasyPost;
use VCR\VCR;

class CreditCardTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test funding a credit card.
     *
     * @return void
     */
    public function testFund()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real credit card in tests.');

        VCR::insertCassette('credit_card/fund.yml');

        $credit_card = CreditCard::fund(2000, 'primary');

        $this->assertTrue($credit_card != null);
    }

    /**
     * Test deleting a credit card.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real credit card in tests.');

        VCR::insertCassette('credit_card/delete.yml');

        $delete_credit_card = CreditCard::delete('card_1234');

        $this->assertTrue($delete_credit_card != null);
    }
}
