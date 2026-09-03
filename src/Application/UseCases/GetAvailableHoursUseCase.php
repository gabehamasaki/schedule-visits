<?php

namespace App\Application\UseCases;

use App\Application\DTOs\GetAvailableHoursDTO;
use App\Application\DTOs\AvailableHoursResponseDTO; // NEW
use App\Domain\Repositories\AppointmentRepositoryInterface;

class GetAvailableHoursUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
    ) {}

    public function execute(GetAvailableHoursDTO $dto): AvailableHoursResponseDTO
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
            '18:00',
        ];

        $bookedHours = $this->appointmentRepository->getBookedHours($dto->vehicleId, $dto->date);

        $formattedBookedHours = array_map(function (string $time) {
            return substr($time, 0, 5);
        }, $bookedHours);

        $availableHours = array_diff($businessHours, $formattedBookedHours);

        return new AvailableHoursResponseDTO(array_values($availableHours));
    }
}
