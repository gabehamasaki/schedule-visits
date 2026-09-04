<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\AvailabilityRepositoryInterface;
use PDO;

class PdoAvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function __construct(private PDO $connection) {}

    /**
     * @return array<string, string[]>
     */
    public function findAvailableSlots(int $vehicleId, string $fromDate): array
    {
        $stmt = $this->connection->prepare("
            SELECT s.slot_date, s.slot_time
            FROM vehicle_availability_slots s
            LEFT JOIN appointments a
                   ON a.vehicle_id = s.vehicle_id
                  AND a.appointment_date = s.slot_date
                  AND a.appointment_time = s.slot_time
            WHERE s.vehicle_id = :vehicleId
              AND s.slot_date >= :fromDate
              AND a.id IS NULL
            ORDER BY s.slot_date, s.slot_time
        ");

        $stmt->execute([
            'vehicleId' => $vehicleId,
            'fromDate'  => $fromDate,
        ]);

        $slots = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $slots[$row['slot_date']][] = substr((string) $row['slot_time'], 0, 5);
        }

        return $slots;
    }

    /**
     * @return string[]
     */
    public function findAvailableSlotsForDate(int $vehicleId, string $date): array
    {
        $stmt = $this->connection->prepare("
            SELECT s.slot_time
            FROM vehicle_availability_slots s
            LEFT JOIN appointments a
                   ON a.vehicle_id = s.vehicle_id
                  AND a.appointment_date = s.slot_date
                  AND a.appointment_time = s.slot_time
            WHERE s.vehicle_id = :vehicleId
              AND s.slot_date = :date
              AND a.id IS NULL
            ORDER BY s.slot_time
        ");

        $stmt->execute([
            'vehicleId' => $vehicleId,
            'date'      => $date,
        ]);

        return array_map(
            fn(string $time): string => substr($time, 0, 5),
            $stmt->fetchAll(PDO::FETCH_COLUMN),
        );
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
}
