<?php

namespace App\Application\DTOs;

class ScheduleVisitDTO
{
    public function __construct(
        public readonly int $vehicleId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $date,
        public readonly string $time
    ) {}
}
