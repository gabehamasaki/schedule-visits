<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Domain\ValueObjects\BusinessHours;
use App\Infrastructure\Database\PdoConnection;
use Dotenv\Dotenv;

// In Docker the settings arrive as real environment variables, so the .env
// file is optional here: safeLoad() keeps both setups working.
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$schedule = require __DIR__ . '/../config/schedule.php';
date_default_timezone_set($schedule['timezone']);

try {
    $businessHours = BusinessHours::fromRange(
        $schedule['first_slot'],
        $schedule['last_slot'],
        $schedule['slot_minutes'],
    );

    $daysAhead = $schedule['days_ahead'];
    $timezone = new DateTimeZone($schedule['timezone']);
    $today = (new DateTimeImmutable('now', $timezone))->setTime(0, 0);

    $pdo = PdoConnection::getInstance();

    $vehicleIds = $pdo->query("SELECT id FROM vehicles ORDER BY id")?->fetchAll(PDO::FETCH_COLUMN) ?: [];

    if ($vehicleIds === []) {
        echo "[Slots] No vehicles found. Run bin/migrate.php first.\n";
        exit(0);
    }

    echo "[Slots] Generating {$daysAhead} days of slots for " . count($vehicleIds) . " vehicles...\n";

    // ON CONFLICT keeps the command idempotent, so running it again only
    // extends the horizon instead of duplicating the existing schedule.
    $stmt = $pdo->prepare("
        INSERT INTO vehicle_availability_slots (vehicle_id, slot_date, slot_time)
        VALUES (:vehicleId, :slotDate, :slotTime)
        ON CONFLICT (vehicle_id, slot_date, slot_time) DO NOTHING
    ");

    $inserted = 0;
    $pdo->beginTransaction();

    foreach ($vehicleIds as $vehicleId) {
        for ($offset = 0; $offset < $daysAhead; $offset++) {
            $slotDate = $today->modify("+{$offset} days")->format('Y-m-d');

            foreach ($businessHours->slots() as $slotTime) {
                $stmt->execute([
                    'vehicleId' => (int) $vehicleId,
                    'slotDate'  => $slotDate,
                    'slotTime'  => $slotTime,
                ]);

                $inserted += $stmt->rowCount();
            }
        }
    }

    $pdo->commit();

    echo "[Slots] Done. {$inserted} new slots inserted.\n";
} catch (Exception $e) {
    echo "[Slots] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
