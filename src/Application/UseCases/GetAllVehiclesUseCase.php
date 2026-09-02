<?php

namespace App\Application\UseCases;

use App\Application\DTOs\VehicleResponseDTO;
use App\Domain\Repositories\VehicleRepositoryInterface;

class GetAllVehiclesUseCase
{
    public function __construct(private VehicleRepositoryInterface $vehicleRepository) {}

    /**
     * @return VehicleResponseDTO[]
     */
    public function execute(): array
    {
        $vehicles = $this->vehicleRepository->findAll();
        return array_map(function ($vehicle) {
            return new VehicleResponseDTO(
                id: $vehicle->getId(),
                brand: $vehicle->getBrand(),
                model: $vehicle->getModel(),
                version: $vehicle->getVersion(),
                price: $vehicle->getPrice(),
                location: $vehicle->getLocation(),
                imageUrl: $vehicle->getImageUrl()
            );
        }, $vehicles);
    }
}
