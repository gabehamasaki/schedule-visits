<?php

namespace App\Application\DTOs;

class AvailableHoursResponseDTO implements \JsonSerializable
{
    /**
     * @param string[] $availableHours
     */
    public function __construct(
        public readonly array $availableHours,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'availableHours' => $this->availableHours,
        ];
    }
}
