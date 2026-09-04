<?php

namespace Tests\Unit\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Domain\Exceptions\ConflictException;
use App\Infrastructure\Repositories\PdoAppointmentRepository;
use App\Domain\Entities\Appointment;
use PDO;
use PDOException;
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

    public function testSaveThrowsConflictExceptionOnUniqueViolation(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        $uniqueViolation = new PDOException('duplicate key value violates unique constraint');
        $uniqueViolation->errorInfo = ['23505', 7, 'duplicate key value violates unique constraint'];

        $stmtMock->method('execute')->willThrowException($uniqueViolation);

        $repository = new PdoAppointmentRepository($pdoMock);

        $this->expectException(ConflictException::class);

        $repository->save(new Appointment(null, 1, 'John', 'john@test.com', '123', '2026-09-02', '10:00'));
    }

    public function testSaveRethrowsPdoExceptionForOtherErrors(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);

        $connectionError = new PDOException('connection refused');
        $connectionError->errorInfo = ['08006', 7, 'connection refused'];

        $stmtMock->method('execute')->willThrowException($connectionError);

        $repository = new PdoAppointmentRepository($pdoMock);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('connection refused');

        $repository->save(new Appointment(null, 1, 'John', 'john@test.com', '123', '2026-09-02', '10:00'));
    }
}
