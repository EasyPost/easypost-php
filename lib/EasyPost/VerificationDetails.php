<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @param int $latitude
 * @param int $longitude
 * @param string $time_zone
 */
class VerificationDetails extends EasyPostObject
{
    public $latitude;
    public $longitude;
    public $time_zone;
}
