<?php

namespace App\Application\DTOs;

class DayAvailabilityDTO implements \JsonSerializable
{
    /**
     * @param string[] $availableHours
     */
    public function __construct(
        public readonly string $date,
        public readonly array $availableHours,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date,
            'availableHours' => $this->availableHours,
        ];
    }
}
