<?php

namespace App\Exceptions;

use Throwable;
use Exception;

class UnAuthorizedException extends Exception
{
    public function __construct($message = null, $code = 403, Throwable $previous = null)
    {
        $message ??= "UnAuthorized.";
        parent::__construct($message, $code, $previous);
    }
}
