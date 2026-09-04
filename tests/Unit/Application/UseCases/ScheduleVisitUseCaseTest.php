<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Application\UseCases\GetAvailableHoursUseCase;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Entities\Appointment;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\DTOs\AvailableHoursResponseDTO;
use App\Application\DTOs\AppointmentResponseDTO;
use App\Application\DTOs\VehicleResponseDTO;
use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Domain\ValueObjects\BusinessHours;

class ScheduleVisitUseCaseTest extends TestCase
{
    private MockObject&AppointmentRepositoryInterface $appointmentRepoMock;
    private MockObject&GetVehicleUseCase $getVehicleUseCaseMock;
    private MockObject&GetAvailableHoursUseCase $getAvailableHoursMock;
    private ScheduleVisitUseCase $useCase;

    protected function setUp(): void
    {
        $this->appointmentRepoMock = $this->createMock(AppointmentRepositoryInterface::class);
        $this->getVehicleUseCaseMock = $this->createMock(GetVehicleUseCase::class);
        $this->getAvailableHoursMock = $this->createMock(GetAvailableHoursUseCase::class);

        $this->useCase = new ScheduleVisitUseCase(
            $this->appointmentRepoMock,
            $this->getVehicleUseCaseMock,
            $this->getAvailableHoursMock,
            BusinessHours::fromRange('09:00', '18:00', 60)
        );
    }

    public function testThrowsExceptionIfVehicleNotFound(): void
    {
        $this->getVehicleUseCaseMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new NotFoundException('Vehicle not found.'));

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessageIs('Vehicle not found.');

        $dto = new ScheduleVisitDTO(999, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $this->useCase->execute($dto);
    }

    public function testThrowsExceptionIfTimeSlotIsUnavailable(): void
    {
        $vehicle = new VehicleResponseDTO(1, 'VW', 'Polo', '1.0', 70000.0, 'SP', 'url');

        $this->getVehicleUseCaseMock->method('execute')->willReturn($vehicle);

        // Simulates that only 11:00 is available, but user wants 10:00
        $this->getAvailableHoursMock->method('execute')
            ->willReturn(new AvailableHoursResponseDTO(['11:00']));

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessageIs('This time slot is already booked.');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $this->useCase->execute($dto);
    }

    public function testSchedulesSuccessfullyAndReturnsResponseDTO(): void
    {
        $vehicle = new VehicleResponseDTO(1, 'VW', 'Polo', '1.0', 70000.0, 'SP', 'url');

        $this->getVehicleUseCaseMock->method('execute')->willReturn($vehicle);

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

    public function testThrowsValidationExceptionForTimeOutsideBusinessHours(): void
    {
        // 19:00 is a well formed HH:MM, but it is not part of the 09:00-18:00 grid,
        // so it must be rejected before any vehicle or availability lookup happens.
        $this->getVehicleUseCaseMock->expects($this->never())->method('execute');
        $this->getAvailableHoursMock->expects($this->never())->method('execute');
        $this->appointmentRepoMock->expects($this->never())->method('save');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '19:00');

        try {
            $this->useCase->execute($dto);
            $this->fail('Expected a ValidationException to be thrown.');
        } catch (ValidationException $e) {
            $this->assertSame(
                ['time' => 'The requested time is outside of business hours.'],
                $e->getDetails()
            );
        }
    }
}
