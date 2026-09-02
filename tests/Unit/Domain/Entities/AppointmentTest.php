<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use App\Domain\Entities\Appointment;

class AppointmentTest extends TestCase
{
    public function testAppointmentCanBeInstantiatedAndReturnsCorrectValues(): void
    {
        $appointment = new Appointment(
            id: 10,
            vehicleId: 1,
            customerName: 'Gabriel',
            customerEmail: 'gabriel@example.com',
            customerPhone: '11999999999',
            appointmentDate: '2026-09-02',
            appointmentTime: '14:00'
        );

        $this->assertEquals(10, $appointment->getId());
        $this->assertEquals(1, $appointment->getVehicleId());
        $this->assertEquals('Gabriel', $appointment->getCustomerName());

        $arrayData = $appointment->toArray();
        $this->assertArrayHasKey('customerEmail', $arrayData);
        $this->assertEquals('2026-09-02', $arrayData['appointmentDate']);
    }
}
