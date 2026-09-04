<?php

namespace App\Domain\Repositories;

interface AvailabilityRepositoryInterface
{
    /**
     * Slots that are offered and still free, grouped by date.
     *
     * @return array<string, string[]> Date in YYYY-MM-DD => times in HH:MM
     */
    public function findAvailableSlots(int $vehicleId, string $fromDate): array;

    /**
     * @return string[] Times in HH:MM
     */
    public function findAvailableSlotsForDate(int $vehicleId, string $date): array;

    /**
     * Whether the schedule offers this slot at all, regardless of it being booked.
     */
    public function slotExists(int $vehicleId, string $date, string $time): bool;
}
