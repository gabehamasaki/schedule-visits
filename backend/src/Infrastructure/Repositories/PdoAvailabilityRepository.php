<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\AvailabilityRepositoryInterface;
use PDO;

class PdoAvailabilityRepository implements AvailabilityRepositoryInterface
{

    private const SLOTS_QUERY = "
        SELECT s.slot_date, s.slot_time, CASE WHEN a.id IS NULL THEN 1 ELSE 0 END AS is_free
        FROM vehicle_availability_slots s
        LEFT JOIN appointments a
               ON a.vehicle_id = s.vehicle_id
              AND a.appointment_date = s.slot_date
              AND a.appointment_time = s.slot_time
        WHERE s.vehicle_id = :vehicleId
          AND s.slot_date %s :date
        ORDER BY s.slot_date, s.slot_time
    ";

    public function __construct(private PDO $connection) {}

    /**
     * @return array<string, array<string, bool>>
     */
    public function findSlots(int $vehicleId, string $fromDate): array
    {
        $rows = $this->fetchSlots($vehicleId, $fromDate, '>=');

        $slots = [];

        foreach ($rows as $row) {
            $slots[(string) $row['slot_date']][$this->toHourAndMinute($row['slot_time'])] = (bool) (int) $row['is_free'];
        }

        return $slots;
    }

    /**
     * @return array<string, bool>
     */
    public function findSlotsForDate(int $vehicleId, string $date): array
    {
        $slots = [];

        foreach ($this->fetchSlots($vehicleId, $date, '=') as $row) {
            $slots[$this->toHourAndMinute($row['slot_time'])] = (bool) (int) $row['is_free'];
        }

        return $slots;
    }

    public function slotExists(int $vehicleId, string $date, string $time): bool
    {
        $stmt = $this->connection->prepare("
            SELECT 1 FROM vehicle_availability_slots
            WHERE vehicle_id = :vehicleId AND slot_date = :date AND slot_time = :time
        ");

        $stmt->execute([
            'vehicleId' => $vehicleId,
            'date'      => $date,
            'time'      => $time,
        ]);

        return $stmt->fetch() !== false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchSlots(int $vehicleId, string $date, string $comparison): array
    {
        $stmt = $this->connection->prepare(sprintf(self::SLOTS_QUERY, $comparison));

        $stmt->execute([
            'vehicleId' => $vehicleId,
            'date'      => $date,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function toHourAndMinute(mixed $time): string
    {
        return substr((string) $time, 0, 5);
    }
}
