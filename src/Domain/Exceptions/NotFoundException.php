<?php

namespace App\Domain\Exceptions;

/**
 * The requested resource does not exist.
 */
class NotFoundException extends DomainException
{
    public function __construct(string $message = 'Resource not found.')
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
