<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Appointment;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use PDO;

class PdoAppointmentRepository implements AppointmentRepositoryInterface
{
    public function __construct(private PDO $connection) {}

    public function save(Appointment $appointment): Appointment
    {
        $stmt = $this->connection->prepare("
            INSERT INTO appointments (vehicle_id, customer_name, customer_email, customer_phone, appointment_date, appointment_time)
            VALUES (:vehicleId, :name, :email, :phone, :date, :time)
        ");

        $stmt->execute([
            'vehicleId' => $appointment->getVehicleId(),
            'name'      => $appointment->getCustomerName(),
            'email'     => $appointment->getCustomerEmail(),
            'phone'     => $appointment->getCustomerPhone(),
            'date'      => $appointment->getAppointmentDate(),
            'time'      => $appointment->getAppointmentTime(),
        ]);

        return new Appointment(
            id: (int) $this->connection->lastInsertId(),
            vehicleId: $appointment->getVehicleId(),
            customerName: $appointment->getCustomerName(),
            customerEmail: $appointment->getCustomerEmail(),
            customerPhone: $appointment->getCustomerPhone(),
            appointmentDate: $appointment->getAppointmentDate(),
            appointmentTime: $appointment->getAppointmentTime(),
        );
    }

    public function getBookedHours(int $vehicleId, string $date): array
    {
        $stmt = $this->connection->prepare("
            SELECT appointment_time FROM appointments
            WHERE vehicle_id = :vehicleId AND appointment_date = :date
        ");

        $stmt->execute([
            'vehicleId' => $vehicleId,
            'date'      => $date,
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
