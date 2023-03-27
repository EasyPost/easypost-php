<?php

namespace EasyPost\Exception\General;

use EasyPost\Constant\Constants;

class EndOfPaginationException extends \Exception
{
    /**
     * EndOfPaginationException constructor.
     *
     * @param string $message
     */
    public function __construct()
    {
        parent::__construct(Constants::END_OF_PAGINATION);
    }
}
