<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AvailabilityResponseDTO;
use App\Application\DTOs\DayAvailabilityDTO;
use App\Application\DTOs\GetAvailabilityDTO;
use App\Application\DTOs\SlotDTO;
use App\Domain\Clock\ClockInterface;
use App\Domain\Repositories\AvailabilityRepositoryInterface;
use DateTimeImmutable;

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
            ? $this->availabilityRepository->findSlots($dto->vehicleId, $today)
            : $this->slotsForSingleDate($dto->vehicleId, $dto->date, $today);

        $days = [];

        foreach ($slotsByDate as $date => $slots) {
            $days[] = new DayAvailabilityDTO(
                date: (string) $date,
                slots: $this->toSlotDTOs((string) $date, $slots, $now),
            );
        }

        return new AvailabilityResponseDTO($dto->vehicleId, $days);
    }

    /**
     *
     * @return array<string, array<string, bool>>
     */
    private function slotsForSingleDate(int $vehicleId, string $date, string $today): array
    {
        if ($date < $today) {
            return [$date => []];
        }

        return [$date => $this->availabilityRepository->findSlotsForDate($vehicleId, $date)];
    }

    /**
     *
     * @param array<string, bool> $slots
     * @return SlotDTO[]
     */
    private function toSlotDTOs(string $date, array $slots, DateTimeImmutable $now): array
    {
        $isToday = $date === $now->format('Y-m-d');
        $currentTime = $now->format('H:i');

        $slotDTOs = [];

        foreach ($slots as $time => $isFree) {
            // A slot that already started carries no information: leave it out
            if ($isToday && (string) $time <= $currentTime) {
                continue;
            }

            $slotDTOs[] = new SlotDTO((string) $time, $isFree);
        }

        return $slotDTOs;
    }
}
