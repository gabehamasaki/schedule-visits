<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\GetAvailabilityDTO;
use App\Application\DTOs\ScheduleVisitDTO;
use App\Application\UseCases\GetAvailabilityUseCase;
use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\ValidationException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class AvailabilityController
{
    public function __construct(
        protected GetVehicleUseCase $getVehicleUseCase,
        protected GetAvailabilityUseCase $getAvailabilityUseCase,
    ) {}

    public function show(Request $request): Response
    {
        $vehicleId = $request->paramInt('id');
        if ($vehicleId === null) {
            throw new ValidationException(['id' => 'Vehicle ID must be a valid integer.']);
        }

        // The date filter is optional: without it the whole upcoming schedule is returned
        $date = $request->query('date');
        if ($date !== null && !ScheduleVisitDTO::isValidDate($date)) {
            throw new ValidationException(['date' => 'Date must be a valid date (YYYY-MM-DD).']);
        }

        $vehicle = $this->getVehicleUseCase->execute($vehicleId);

        $availability = $this->getAvailabilityUseCase->execute(
            new GetAvailabilityDTO(vehicleId: $vehicle->id, date: $date),
        );

        return Response::success($availability);
    }
}
