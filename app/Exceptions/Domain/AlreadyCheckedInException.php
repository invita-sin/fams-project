<?php

namespace App\Exceptions\Domain;

class AlreadyCheckedInException extends DomainException
{
    public function __construct(string $message = 'Already checked in today')
    {
        parent::__construct($message);
    }
}
