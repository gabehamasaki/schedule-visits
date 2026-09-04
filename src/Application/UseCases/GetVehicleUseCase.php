<?php

namespace App\Application\UseCases;

use App\Application\DTOs\VehicleResponseDTO;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Repositories\VehicleRepositoryInterface;

class GetVehicleUseCase
{
    public function __construct(protected VehicleRepositoryInterface $vehicleRepository) {}

    public function execute(int $vehicleId): VehicleResponseDTO
    {

        $vehicle = $this->vehicleRepository->findById($vehicleId);

        if (is_null($vehicle)) {
            throw new NotFoundException('Vehicle not found.');
        }

        return new VehicleResponseDTO(
            $vehicle->getId(),
            $vehicle->getBrand(),
            $vehicle->getModel(),
            $vehicle->getVersion(),
            $vehicle->getPrice(),
            $vehicle->getLocation(),
            $vehicle->getImageUrl()
        );
    }
}
