<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AvailabilityResponseDTO;
use App\Application\DTOs\DayAvailabilityDTO;
use App\Application\DTOs\GetAvailabilityDTO;
use App\Domain\Clock\ClockInterface;
use App\Domain\Repositories\AvailabilityRepositoryInterface;

class GetAvailabilityUseCase
{
    public function __construct(
        private AvailabilityRepositoryInterface $availabilityRepository,
        private ClockInterface $clock,
    ) {}

    public function execute(GetAvailabilityDTO $dto): AvailabilityResponseDTO
    {
        $now = $this->clock->now();
        $today = $now->format('Y-m-d');

        $slotsByDate = $dto->date === null
            ? $this->availabilityRepository->findAvailableSlots($dto->vehicleId, $today)
            : $this->slotsForSingleDate($dto->vehicleId, $dto->date, $today);

        $days = [];

        foreach ($slotsByDate as $date => $hours) {
            $days[] = new DayAvailabilityDTO(
                date: $date,
                availableHours: $date === $today ? $this->stillAhead($hours, $now) : $hours,
            );
        }

        return new AvailabilityResponseDTO($dto->vehicleId, $days);
    }

    /**
     * A single date always answers with one day, even when nothing is left,
     * so the client can tell "fully booked" apart from "unknown date".
     *
     * @return array<string, string[]>
     */
    private function slotsForSingleDate(int $vehicleId, string $date, string $today): array
    {
        if ($date < $today) {
            return [$date => []];
        }

        return [$date => $this->availabilityRepository->findAvailableSlotsForDate($vehicleId, $date)];
    }

    /**
     * @param string[] $hours
     * @return string[]
     */
    private function stillAhead(array $hours, \DateTimeImmutable $now): array
    {
        $currentTime = $now->format('H:i');

        return array_values(array_filter(
            $hours,
            fn(string $hour): bool => $hour > $currentTime,
        ));
    }
}
