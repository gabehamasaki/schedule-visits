<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Application\UseCases\GetAvailableHoursUseCase;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use App\Domain\Entities\Vehicle;
use App\Domain\Entities\Appointment;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\DTOs\AvailableHoursResponseDTO;
use App\Application\DTOs\AppointmentResponseDTO;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;

class ScheduleVisitUseCaseTest extends TestCase
{
    private MockObject&AppointmentRepositoryInterface $appointmentRepoMock;
    private MockObject&VehicleRepositoryInterface $vehicleRepoMock;
    private MockObject&GetAvailableHoursUseCase $getAvailableHoursMock;
    private ScheduleVisitUseCase $useCase;

    protected function setUp(): void
    {
        $this->appointmentRepoMock = $this->createMock(AppointmentRepositoryInterface::class);
        $this->vehicleRepoMock = $this->createMock(VehicleRepositoryInterface::class);
        $this->getAvailableHoursMock = $this->createMock(GetAvailableHoursUseCase::class);

        $this->useCase = new ScheduleVisitUseCase(
            $this->appointmentRepoMock,
            $this->vehicleRepoMock,
            $this->getAvailableHoursMock,
        );
    }

    public function testThrowsExceptionIfVehicleNotFound(): void
    {
        $this->vehicleRepoMock->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Vehicle not found.');

        $dto = new ScheduleVisitDTO(999, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $this->useCase->execute($dto);
    }

    public function testThrowsExceptionIfTimeSlotIsUnavailable(): void
    {
        $vehicle = new Vehicle(1, 'VW', 'Polo', '1.0', 70000.0, 'SP', 'url');

        $this->vehicleRepoMock->method('findById')->willReturn($vehicle);

        // Simulates that only 11:00 is available, but user wants 10:00
        $this->getAvailableHoursMock->method('execute')
            ->willReturn(new AvailableHoursResponseDTO(['11:00']));

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('This time slot is not available or outside business hours.');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $this->useCase->execute($dto);
    }

    public function testSchedulesSuccessfullyAndReturnsResponseDTO(): void
    {
        $vehicle = new Vehicle(1, 'VW', 'Polo', '1.0', 70000.0, 'SP', 'url');

        $this->vehicleRepoMock->method('findById')->willReturn($vehicle);

        // Simulates that 10:00 IS available
        $this->getAvailableHoursMock->method('execute')
            ->willReturn(new AvailableHoursResponseDTO(['10:00', '11:00']));

        $expectedAppointment = new Appointment(15, 1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');

        $this->appointmentRepoMock->expects($this->once())
            ->method('save')
            ->willReturn($expectedAppointment);

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $response = $this->useCase->execute($dto);

        $this->assertInstanceOf(AppointmentResponseDTO::class, $response);
        $this->assertEquals(15, $response->id);
    }
}
