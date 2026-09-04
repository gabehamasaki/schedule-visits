<?php

namespace App\Application\DTOs;

class GetAvailableHoursDTO implements \JsonSerializable
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $date,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'vehicleId' => $this->vehicleId,
            'date' => $this->date,
        ];
    }
}
