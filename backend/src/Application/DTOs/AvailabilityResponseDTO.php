<?php

namespace App\Application\DTOs;

class AvailabilityResponseDTO implements \JsonSerializable
{
    /**
     * @param DayAvailabilityDTO[] $days
     */
    public function __construct(
        public readonly int $vehicleId,
        public readonly array $days,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'vehicleId' => $this->vehicleId,
            'days' => $this->days,
        ];
    }
}
