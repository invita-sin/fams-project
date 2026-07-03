<?php

namespace App\Exceptions\Domain;

abstract class DomainException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
