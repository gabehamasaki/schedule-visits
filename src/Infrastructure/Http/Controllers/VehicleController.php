<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\GetAllVehiclesUseCase;
use App\Application\UseCases\GetVehicleUseCase;
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
            return Response::badRequest('Vehicle ID is required.');
        }

        $vehicle = $this->getVehicleUseCase->execute($vehicleId);
        if ($vehicle === null) {
            return Response::notFound('Vehicle not found.');
        }

        return Response::success($vehicle);
    }
}
