<?php

namespace App\Exceptions\Domain;

class ResourceNotFoundException extends DomainException
{
    public function __construct(string $message = 'Resource not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
