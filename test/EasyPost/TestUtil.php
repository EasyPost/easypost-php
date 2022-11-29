<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use VCR\VCR;

class TestUtil
{
    /**
     * Runs all the logic required to setup a VCR test.
     *
     * @param string $apiKeyEnvVar
     * @return void
     */
    public static function setupVcrTests($apiKeyEnvVar)
    {
        if ($apiKeyEnvVar === 'PARTNER_USER_PROD_API_KEY') {
            $apiKeyEnvVar = getenv('PARTNER_USER_PROD_API_KEY') !== false ? getenv('PARTNER_USER_PROD_API_KEY') : '123';
            EasyPost::setApiKey($apiKeyEnvVar);
        } else {
            EasyPost::setApiKey(getenv($apiKeyEnvVar));
        }
        VCR::turnOn();
    }

    /**
     * Runs all the logic required to teardown a VCR test.
     *
     * @return void
     */
    public static function teardownVcrTests()
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Inserts a cassette for use in tests and checks if it's expired.
     *
     * @param string $cassettePath
     * @return void
     */
    public static function setupCassette($cassettePath)
    {
        self::checkExpiredCassette($cassettePath);
        VCR::insertCassette($cassettePath);
    }

    /**
     * Checks for an expired cassette and warns if it is too old and must be re-recorded.
     *
     * @return void
     */
    private static function checkExpiredCassette($cassettePath)
    {
        $fullCassettePath = "test/cassettes/$cassettePath";
        $secondsInDay = 86400;
        $expirationDays = 180;
        $expirationSeconds = $secondsInDay * $expirationDays;

        if (file_exists($fullCassettePath)) {
            $cassetteTimestamp = filemtime($fullCassettePath);
            $expirationTimestamp = $cassetteTimestamp + $expirationSeconds;
            $currentTimestamp = time();

            if ($currentTimestamp > $expirationTimestamp) {
                error_log("$fullCassettePath is older than $expirationDays days and has expired. Please re-record the cassette.");
            }
        }
    }
}
