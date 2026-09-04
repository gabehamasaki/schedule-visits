<?php

namespace App\Domain\Exceptions;

/**
 * Input did not pass format validation (missing field, malformed email, etc).
 */
class ValidationException extends DomainException
{
    /**
     * @param array<string, string> $errors Message per invalid field.
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Validation failed.',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * @return array<string, string>
     */
    public function getDetails(): array
    {
        return $this->errors;
    }
}
