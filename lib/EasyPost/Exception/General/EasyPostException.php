<?php

namespace EasyPost\Exception\General;

/**
 * @package EasyPost
 * @param string $message
 */
class EasyPostException extends \Exception
{
    /**
     * EasyPostException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
