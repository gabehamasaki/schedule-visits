<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Appointment;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use App\Domain\Repositories\VehicleRepositoryInterface;
use Exception;

class ScheduleVisitUseCase
{



    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private VehicleRepositoryInterface $vehicleRepository,
        private GetAvailableHoursUseCase $getAvailableHoursUseCase
    ) {}

    public function execute(array $data): Appointment
    {
        $vehicleID = (int) $data['vehicle_id'] ?? null;

        $date = $data['date'] ?? null;
        $time = $data['time'] ?? null;

        // 1. Checkl if the vehicle exists
        $vehicle = $this->vehicleRepository->findById($vehicleID);
        if (!$vehicle) {
            throw new Exception('Vehicle not found.');
        }

        // 2. Get the dynamically available hours for the given vehicle and date
        $availableHours = $this->getAvailableHoursUseCase->execute($vehicleID, $date);

        // 3. Validate if the requested time is actually available and within business hours
        if (!in_array($time, $availableHours)) {
            throw new Exception("This time slot is not available or outside business hours.");
        }

        // 4. Create and save the appointment
        $appointment = new Appointment(
            id: null,
            vehicleId: $vehicleID,
            customerName: $data['customer_name'] ?? '',
            customerEmail: $data['customer_email'] ?? '',
            customerPhone: $data['customer_phone'] ?? '',
            appointmentDate: $date,
            appointmentTime: $time
        );

        $savedAppointment = $this->appointmentRepository->save($appointment);

        return $savedAppointment;
    }
}
