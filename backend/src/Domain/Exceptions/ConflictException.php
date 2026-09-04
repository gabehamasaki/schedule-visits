<?php

namespace App\Domain\Exceptions;

/**
 * The request is valid but conflicts with the current state of the resource,
 * such as booking a time slot that is already taken.
 */
class ConflictException extends DomainException
{
    public function __construct(string $message = 'Resource conflict.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 409;
    }
}
