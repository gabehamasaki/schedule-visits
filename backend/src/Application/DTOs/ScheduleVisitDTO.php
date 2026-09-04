<?php

namespace App\Application\DTOs;

use App\Domain\Exceptions\ValidationException;

class ScheduleVisitDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $date,
        public readonly string $time,
    ) {}

    public function validate(): self
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Name is required.';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email is required.';
        }
        if (!preg_match('/^\d{10,11}$/', $this->phone)) {
            $errors['phone'] = 'Phone must contain 10 or 11 digits.';
        }
        if (!self::isValidDate($this->date)) {
            $errors['date'] = 'Date must be a valid date (YYYY-MM-DD).';
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $this->time)) {
            $errors['time'] = 'Time must be in HH:MM format.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this;
    }

    public static function isValidDate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'vehicleId' => $this->vehicleId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date' => $this->date,
            'time' => $this->time,
        ];
    }
}
