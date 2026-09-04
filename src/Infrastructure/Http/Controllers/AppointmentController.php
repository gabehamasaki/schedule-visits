<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\UseCases\GetVehicleUseCase;
use App\Application\UseCases\ScheduleVisitUseCase;
use App\Domain\Exceptions\ValidationException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class AppointmentController
{
    public function __construct(
        protected ScheduleVisitUseCase $scheduleVisitUseCase,
        protected GetVehicleUseCase $getVehicleUseCase,
    ) {}

    public function store(Request $request): Response
    {
        $vehicleId = $request->paramInt('id');

        if ($vehicleId === null) {
            throw new ValidationException(['id' => 'Vehicle ID must be a valid integer.']);
        }

        $vehicle = $this->getVehicleUseCase->execute($vehicleId);

        $data =  new ScheduleVisitDTO(
            vehicleId: $vehicleId,
            name: $request->input('name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            date: $request->input('date'),
            time: $request->input('time'),
        )->validate();

        $result = $this->scheduleVisitUseCase->execute($data);

        return Response::created($result, 'Appointment scheduled successfully.');
    }
}
