<?php

namespace App\Exceptions\Domain;

class ScheduleNotFoundException extends DomainException
{
    public function __construct(string $message = 'No active schedule assigned for today')
    {
        parent::__construct($message);
    }
}
