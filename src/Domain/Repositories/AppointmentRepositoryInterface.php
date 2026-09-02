<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Appointment;

interface AppointmentRepositoryInterface
{
    public function save(Appointment $appointment): Appointment;
    /** @return string[] */
    public function getBookedHours(int $vehicleId, string $date): array;
}
