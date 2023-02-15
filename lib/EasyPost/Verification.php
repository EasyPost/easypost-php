<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @param bool success
 * @param FieldError[] errors
 * @param VerificationDetails details
 */
class Verification extends EasyPostObject
{
    public $success;
    public $errors;
    public $details;
}
