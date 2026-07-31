<?php

namespace App\Exceptions;

use Exception;

class InsufficientGemsException extends Exception
{
    public function __construct(public int $required, public int $available)
    {
        parent::__construct("This request needs about {$required} gems but you only have {$available}. Top up or switch to a cheaper model/thinking mode.");
    }
}
