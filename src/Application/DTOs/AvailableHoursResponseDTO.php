<?php

namespace App\Application\DTOs;

class AvailableHoursResponseDTO
{
    /**
     * @param string[] $availableHours
     */
    public function __construct(
        public readonly array $availableHours
    ) {}
}
