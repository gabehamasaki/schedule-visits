<?php

namespace App\Application\DTOs;

class GetAvailableHoursDTO
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $date
    ) {}
}
