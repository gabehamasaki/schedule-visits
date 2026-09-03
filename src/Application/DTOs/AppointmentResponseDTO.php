<?php

namespace App\Application\DTOs;

class AppointmentResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $vehicleId,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly string $customerPhone,
        public readonly string $appointmentDate,
        public readonly string $appointmentTime,
    ) {}
}
