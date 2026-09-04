<?php

namespace App\Application\DTOs;

class AppointmentResponseDTO implements \JsonSerializable
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'vehicleId' => $this->vehicleId,
            'customerName' => $this->customerName,
            'customerEmail' => $this->customerEmail,
            'customerPhone' => $this->customerPhone,
            'appointmentDate' => $this->appointmentDate,
            'appointmentTime' => $this->appointmentTime,
        ];
    }
}
