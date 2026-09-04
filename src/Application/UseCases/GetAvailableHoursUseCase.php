<?php

namespace App\Application\UseCases;

use App\Application\DTOs\GetAvailableHoursDTO;
use App\Application\DTOs\AvailableHoursResponseDTO; // NEW
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\ValueObjects\BusinessHours;

class GetAvailableHoursUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private BusinessHours $businessHours
    ) {}

    public function execute(GetAvailableHoursDTO $dto): AvailableHoursResponseDTO
    {
        $bookedHours = $this->appointmentRepository->getBookedHours($dto->vehicleId, $dto->date);

        $formattedBookedHours = array_map(function (string $time) {
            return substr($time, 0, 5);
        }, $bookedHours);

        $availableHours = array_diff($this->businessHours->slots(), $formattedBookedHours);

        return new AvailableHoursResponseDTO(array_values($availableHours));
    }
}
