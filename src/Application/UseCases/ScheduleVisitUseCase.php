<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AppointmentResponseDTO;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Domain\Clock\ClockInterface;
use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\ValidationException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\AvailabilityRepositoryInterface;
use DateTimeImmutable;

class ScheduleVisitUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private AvailabilityRepositoryInterface $availabilityRepository,
        private GetVehicleUseCase $getVehicleUseCase,
        private ClockInterface $clock,
    ) {}

    public function execute(ScheduleVisitDTO $data): AppointmentResponseDTO
    {
        // 1. Reject a slot in the past before touching the database
        $now = $this->clock->now();
        $requestedAt = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i',
            "{$data->date} {$data->time}",
            $now->getTimezone(),
        );

        if ($requestedAt === false) {
            throw new ValidationException(['date' => 'Date and time must be valid (YYYY-MM-DD and HH:MM).']);
        }

        if ($requestedAt < $now) {
            throw new ValidationException(['date' => 'Cannot schedule a visit in the past.']);
        }

        // 2. Check if the vehicle exists
        $vehicle = $this->getVehicleUseCase->execute($data->vehicleId);

        // 3. The schedule has to offer this slot in the first place
        if (!$this->availabilityRepository->slotExists($vehicle->id, $data->date, $data->time)) {
            throw new ValidationException(['time' => 'The requested time is not offered for this date.']);
        }

        // 4. The slot is offered, so its absence here means someone else took it
        $availableHours = $this->availabilityRepository->findAvailableSlotsForDate($vehicle->id, $data->date);

        if (!in_array($data->time, $availableHours, true)) {
            throw new ConflictException('This time slot is already booked.');
        }

        // 5. Create and save the appointment
        $appointment = new Appointment(
            id: null,
            vehicleId: $vehicle->id,
            customerName: $data->name,
            customerEmail: $data->email,
            customerPhone: $data->phone,
            appointmentDate: $data->date,
            appointmentTime: $data->time,
        );

        $savedAppointment = $this->appointmentRepository->save($appointment);

        return new AppointmentResponseDTO(
            id: $savedAppointment->getId() ?? 0,
            vehicleId: $savedAppointment->getVehicleId(),
            customerName: $savedAppointment->getCustomerName(),
            customerEmail: $savedAppointment->getCustomerEmail(),
            customerPhone: $savedAppointment->getCustomerPhone(),
            appointmentDate: $savedAppointment->getAppointmentDate(),
            appointmentTime: $savedAppointment->getAppointmentTime(),
        );
    }
}
