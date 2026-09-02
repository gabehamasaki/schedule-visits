<?php

namespace App\Domain\Entities;

class Appointment
{
    public function __construct(
        private ?int $id,
        private int $vehicleId,
        private string $customerName,
        private string $customerEmail,
        private string $customerPhone,
        private string $appointmentDate, // YYYY-MM-DD
        private string $appointmentTime  // HH:MM
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }
    public function getCustomerName(): string
    {
        return $this->customerName;
    }
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
    public function getCustomerPhone(): string
    {
        return $this->customerPhone;
    }
    public function getAppointmentDate(): string
    {
        return $this->appointmentDate;
    }
    public function getAppointmentTime(): string
    {
        return $this->appointmentTime;
    }

    public function toArray(): array
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
