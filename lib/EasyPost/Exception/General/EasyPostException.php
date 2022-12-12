<?php

namespace EasyPost\Exception\General;

class EasyPostException extends \Exception
{
    /**
     * EasyPostException constructor.
     *
     * @param string $message
     */
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}
