<?php

namespace App\Exceptions\Domain;

class EmployeeNotFoundException extends DomainException
{
    public function __construct(string $message = 'Employee not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
