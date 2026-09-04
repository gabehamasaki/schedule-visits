<?php

namespace Tests\Unit\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\PdoAvailabilityRepository;
use PDO;
use PDOStatement;

class PdoAvailabilityRepositoryTest extends TestCase
{
    public function testFindAvailableSlotsGroupsTimesByDate(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())->method('prepare')->willReturn($stmtMock);

        // Postgres returns TIME as HH:MM:SS, the API exposes HH:MM
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['slot_date' => '2026-09-05', 'slot_time' => '09:00:00'],
                ['slot_date' => '2026-09-05', 'slot_time' => '10:00:00'],
                ['slot_date' => '2026-09-06', 'slot_time' => '09:00:00'],
            ]);

        $repository = new PdoAvailabilityRepository($pdoMock);
        $slots = $repository->findAvailableSlots(1, '2026-09-05');

        $this->assertEquals(
            [
                '2026-09-05' => ['09:00', '10:00'],
                '2026-09-06' => ['09:00'],
            ],
            $slots,
        );
    }

    public function testFindAvailableSlotsForDateReturnsTimesWithoutSeconds(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn(['09:00:00', '11:00:00']);

        $repository = new PdoAvailabilityRepository($pdoMock);

        $this->assertEquals(['09:00', '11:00'], $repository->findAvailableSlotsForDate(1, '2026-09-05'));
    }

    public function testSlotExistsReflectsWhatTheScheduleOffers(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('fetch')->willReturn(false);

        $repository = new PdoAvailabilityRepository($pdoMock);

        $this->assertFalse($repository->slotExists(1, '2026-09-05', '07:00'));
    }
}
