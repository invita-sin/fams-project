<?php

namespace App\Exceptions\Domain;

class CheckInTooEarlyException extends DomainException
{
    public function __construct(string $message = 'Check-in too early')
    {
        parent::__construct($message);
    }
}
