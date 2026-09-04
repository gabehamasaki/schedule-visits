<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Appointment;
use App\Domain\Exceptions\ConflictException;
use App\Domain\Repositories\AppointmentRepositoryInterface;
use PDO;
use PDOException;

class PdoAppointmentRepository implements AppointmentRepositoryInterface
{
    private const UNIQUE_VIOLATION = '23505';

    public function __construct(private PDO $connection) {}

    public function save(Appointment $appointment): Appointment
    {
        $stmt = $this->connection->prepare("
            INSERT INTO appointments (vehicle_id, customer_name, customer_email, customer_phone, appointment_date, appointment_time)
            VALUES (:vehicleId, :name, :email, :phone, :date, :time)
        ");

        try {
            $stmt->execute([
                'vehicleId' => $appointment->getVehicleId(),
                'name'      => $appointment->getCustomerName(),
                'email'     => $appointment->getCustomerEmail(),
                'phone'     => $appointment->getCustomerPhone(),
                'date'      => $appointment->getAppointmentDate(),
                'time'      => $appointment->getAppointmentTime(),
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === self::UNIQUE_VIOLATION) {
                throw new ConflictException('This time slot is not available or outside business hours.');
            }

            throw $e;
        }

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
