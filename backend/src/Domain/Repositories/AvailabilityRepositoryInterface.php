<?php

namespace App\Domain\Repositories;

interface AvailabilityRepositoryInterface
{
    /**
     * Every slot the schedule offers from the given date on, flagged as free or taken.
     *
     * @return array<string, array<string, bool>> Date in YYYY-MM-DD => time in HH:MM => is free
     */
    public function findSlots(int $vehicleId, string $fromDate): array;

    /**
     * @return array<string, bool> Time in HH:MM => is free
     */
    public function findSlotsForDate(int $vehicleId, string $date): array;

    /**
     * Whether the schedule offers this slot at all, regardless of it being booked.
     */
    public function slotExists(int $vehicleId, string $date, string $time): bool;
}
