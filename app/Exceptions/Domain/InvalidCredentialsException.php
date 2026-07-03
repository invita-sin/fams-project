<?php

namespace App\Exceptions\Domain;

class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = 'Invalid credentials', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
