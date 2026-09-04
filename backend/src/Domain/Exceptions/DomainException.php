<?php

namespace App\Domain\Exceptions;

use Exception;

/**
 * Base class for every expected application failure.
 *
 * Each subclass knows which HTTP status represents it, so the front controller
 * can translate the exception into a response without a mapping table.
 */
abstract class DomainException extends Exception
{
    abstract public function getStatusCode(): int;

    /**
     * Extra details to expose in the error response, if any.
     *
     * @return array<string, string>
     */
    public function getDetails(): array
    {
        return [];
    }
}
