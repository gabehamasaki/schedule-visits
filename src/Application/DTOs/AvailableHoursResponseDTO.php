<?php

namespace App\Application\DTOs;

class AvailableHoursResponseDTO
{
    public function __construct(
        public readonly array $availableHours
    ) {}
}
