<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\GetAllVehiclesUseCase;
use App\Application\UseCases\GetVehicleUseCase;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class VehicleController
{
    public function __construct(
        protected GetVehicleUseCase $getVehicleUseCase,
        protected GetAllVehiclesUseCase $getAllVehiclesUseCase,
    ) {}

    public function index(Request $request): Response
    {
        $vehicles = $this->getAllVehiclesUseCase->execute();
        return Response::success($vehicles);
    }

    public function show(Request $request): Response
    {
        $vehicleId = $request->paramInt('id');
        if ($vehicleId === null) {
            throw new ValidationException(['id' => 'Vehicle ID must be a valid integer.']);
        }

        $vehicle = $this->getVehicleUseCase->execute($vehicleId);
        if ($vehicle === null) {
            throw new NotFoundException('Vehicle not found.');
        }

        return Response::success($vehicle);
    }
}
