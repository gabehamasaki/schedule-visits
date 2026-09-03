<?php

namespace App\Application\UseCases;

use App\Application\DTOs\AppointmentResponseDTO;
use App\Application\DTOs\GetAvailableHoursDTO;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;

class ScheduleVisitUseCase
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private VehicleRepositoryInterface $vehicleRepository,
        private GetAvailableHoursUseCase $getAvailableHoursUseCase,
    ) {}

    public function execute(ScheduleVisitDTO $data): AppointmentResponseDTO
    {
        // 1. Check if the vehicle exists
        $vehicle = $this->vehicleRepository->findById($data->vehicleId);
        if (is_null($vehicle)) {
            throw new NotFoundException('Vehicle not found.');
        }

        // 2. Get the dynamically available hours for the given vehicle and date
        $dto = new GetAvailableHoursDTO(
            vehicleId: $data->vehicleId,
            date: $data->date,
        );
        $availableHoursResponse = $this->getAvailableHoursUseCase->execute($dto);

        if (!in_array($data->time, $availableHoursResponse->availableHours, true)) {
            throw new ConflictException('This time slot is not available or outside business hours.');
        }

        // 4. Create and save the appointment
        $appointment = new Appointment(
            id: null,
            vehicleId: $data->vehicleId,
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
