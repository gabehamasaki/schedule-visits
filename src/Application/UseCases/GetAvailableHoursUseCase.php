<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\AppointmentRepositoryInterface;

class GetAvailableHoursUseCase
{
    private AppointmentRepositoryInterface $appointmentRepository;

    public function __contruct(AppointmentRepositoryInterface $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function execute(int $vehicleId, string $date): array
    {
        $businessHours = [
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00'
        ];

        $bookedHours = $this->appointmentRepository->getBookedHours($vehicleId, $date);

        $formattedBookedHours = array_map(function ($hour) {
            return substr($hour, 0, 5); // Format to HH:MM
        }, $bookedHours);

        $availableHours = array_diff($businessHours, $formattedBookedHours);

        return array_values($availableHours);
    }
}
