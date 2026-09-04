<?php

namespace Tests\Unit\Application\UseCases;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Entities\Appointment;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\DTOs\AppointmentResponseDTO;
use App\Application\DTOs\VehicleResponseDTO;
use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use Tests\Support\FrozenClock;

class ScheduleVisitUseCaseTest extends TestCase
{
    private MockObject&AppointmentRepositoryInterface $appointmentRepoMock;
    private MockObject&AvailabilityRepositoryInterface $availabilityRepoMock;
    private MockObject&GetVehicleUseCase $getVehicleUseCaseMock;
    private ScheduleVisitUseCase $useCase;

    protected function setUp(): void
    {
        $this->appointmentRepoMock = $this->createMock(AppointmentRepositoryInterface::class);
        $this->availabilityRepoMock = $this->createMock(AvailabilityRepositoryInterface::class);
        $this->getVehicleUseCaseMock = $this->createMock(GetVehicleUseCase::class);

        $this->useCase = new ScheduleVisitUseCase(
            $this->appointmentRepoMock,
            $this->availabilityRepoMock,
            $this->getVehicleUseCaseMock,
            new FrozenClock('2026-09-01 08:00'),
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

    public function testThrowsValidationExceptionForSlotInThePast(): void
    {
        // Nothing is looked up: a past slot is rejected before any query
        $this->getVehicleUseCaseMock->expects($this->never())->method('execute');
        $this->availabilityRepoMock->expects($this->never())->method('slotExists');
        $this->appointmentRepoMock->expects($this->never())->method('save');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-08-31', '10:00');

        try {
            $this->useCase->execute($dto);
            $this->fail('Expected a ValidationException to be thrown.');
        } catch (ValidationException $e) {
            $this->assertSame(['date' => 'Cannot schedule a visit in the past.'], $e->getDetails());
        }
    }

    public function testThrowsValidationExceptionWhenSlotIsNotOffered(): void
    {
        $this->getVehicleUseCaseMock->method('execute')->willReturn($this->vehicle());

        // The schedule has no 07:00 slot for that date
        $this->availabilityRepoMock->expects($this->once())
            ->method('slotExists')
            ->with(1, '2026-09-02', '07:00')
            ->willReturn(false);

        $this->appointmentRepoMock->expects($this->never())->method('save');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '07:00');

        try {
            $this->useCase->execute($dto);
            $this->fail('Expected a ValidationException to be thrown.');
        } catch (ValidationException $e) {
            $this->assertSame(
                ['time' => 'The requested time is not offered for this date.'],
                $e->getDetails(),
            );
        }
    }

    public function testThrowsConflictExceptionWhenSlotIsAlreadyBooked(): void
    {
        $this->getVehicleUseCaseMock->method('execute')->willReturn($this->vehicle());

        // The slot is offered, but someone else already took it
        $this->availabilityRepoMock->method('slotExists')->willReturn(true);
        $this->availabilityRepoMock->method('findAvailableSlotsForDate')->willReturn(['11:00']);

        $this->appointmentRepoMock->expects($this->never())->method('save');

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessageIs('This time slot is already booked.');

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $this->useCase->execute($dto);
    }

    public function testSchedulesSuccessfullyAndReturnsResponseDTO(): void
    {
        $this->getVehicleUseCaseMock->method('execute')->willReturn($this->vehicle());

        $this->availabilityRepoMock->method('slotExists')->willReturn(true);
        $this->availabilityRepoMock->method('findAvailableSlotsForDate')->willReturn(['10:00', '11:00']);

        $expectedAppointment = new Appointment(15, 1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');

        $this->appointmentRepoMock->expects($this->once())
            ->method('save')
            ->willReturn($expectedAppointment);

        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11', '2026-09-02', '10:00');
        $response = $this->useCase->execute($dto);

        $this->assertInstanceOf(AppointmentResponseDTO::class, $response);
        $this->assertEquals(15, $response->id);
    }

    private function vehicle(): VehicleResponseDTO
    {
        return new VehicleResponseDTO(1, 'VW', 'Polo', '1.0', 70000.0, 'SP', 'url');
    }
}
