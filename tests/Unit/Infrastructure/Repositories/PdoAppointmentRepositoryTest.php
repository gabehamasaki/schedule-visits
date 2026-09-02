<?php

namespace Tests\Unit\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\PdoAppointmentRepository;
use App\Domain\Entities\Appointment;
use PDO;
use PDOStatement;

class PdoAppointmentRepositoryTest extends TestCase
{
    public function testSaveInsertsAndReturnsAppointmentWithId(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($stmtMock);

        $stmtMock->expects($this->once())
            ->method('execute');

        // Mocks the database auto-increment returning '42'
        $pdoMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn("42");

        $repository = new PdoAppointmentRepository($pdoMock);

        $appointmentToSave = new Appointment(null, 1, 'John', 'john@test.com', '123', '2026-09-02', '10:00');

        $savedAppointment = $repository->save($appointmentToSave);

        $this->assertEquals(42, $savedAppointment->getId());
        $this->assertEquals('John', $savedAppointment->getCustomerName());
    }

    public function testGetBookedHoursReturnsArrayOfTimes(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        // Simulates fetchAll returning a single column array (PDO::FETCH_COLUMN)
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn(['09:00:00', '11:00:00']);

        $repository = new PdoAppointmentRepository($pdoMock);
        $hours = $repository->getBookedHours(1, '2026-09-02');

        $this->assertCount(2, $hours);
        $this->assertEquals('09:00:00', $hours[0]);
    }
}
