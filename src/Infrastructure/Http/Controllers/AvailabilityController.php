<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\GetAvailableHoursDTO;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\UseCases\GetAvailableHoursUseCase;
use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\ValidationException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class AvailabilityController
{
    public function __construct(
        protected GetVehicleUseCase $getVehicleUseCase,
        protected GetAvailableHoursUseCase $getAvailableHoursUseCase,
    ) {}

    public function show(Request $request): Response
    {
        $vehicleId = $request->paramInt('id');
        if ($vehicleId === null) {
            throw new ValidationException(['id' => 'Vehicle ID must be a valid integer.']);
        }

        $date = $request->query('date');
        if ($date === null || !ScheduleVisitDTO::isValidDate($date)) {
            throw new ValidationException(['date' => 'Date must be a valid date (YYYY-MM-DD).']);
        }

        $vehicle = $this->getVehicleUseCase->execute($vehicleId);

        $availableHours = $this->getAvailableHoursUseCase->execute(
            new GetAvailableHoursDTO(vehicleId: $vehicle->id, date: $date)
        );

        return Response::success($availableHours);
    }
}
