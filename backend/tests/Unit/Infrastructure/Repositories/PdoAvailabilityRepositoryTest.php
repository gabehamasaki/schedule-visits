<?php

namespace Tests\Unit\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\PdoAvailabilityRepository;
use PDO;
use PDOStatement;

class PdoAvailabilityRepositoryTest extends TestCase
{
    public function testFindSlotsGroupsTimesByDateAndFlagsTakenOnes(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())->method('prepare')->willReturn($stmtMock);

        // Postgres returns TIME as HH:MM:SS and the flag as an integer
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['slot_date' => '2026-09-05', 'slot_time' => '09:00:00', 'is_free' => 0],
                ['slot_date' => '2026-09-05', 'slot_time' => '10:00:00', 'is_free' => 1],
                ['slot_date' => '2026-09-06', 'slot_time' => '09:00:00', 'is_free' => 1],
            ]);

        $repository = new PdoAvailabilityRepository($pdoMock);

        $this->assertEquals(
            [
                '2026-09-05' => ['09:00' => false, '10:00' => true],
                '2026-09-06' => ['09:00' => true],
            ],
            $repository->findSlots(1, '2026-09-05'),
        );
    }

    public function testFindSlotsForDateReturnsTimesWithoutSeconds(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn([
                ['slot_date' => '2026-09-05', 'slot_time' => '09:00:00', 'is_free' => 1],
                ['slot_date' => '2026-09-05', 'slot_time' => '11:00:00', 'is_free' => 0],
            ]);

        $repository = new PdoAvailabilityRepository($pdoMock);

        $this->assertEquals(['09:00' => true, '11:00' => false], $repository->findSlotsForDate(1, '2026-09-05'));
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
