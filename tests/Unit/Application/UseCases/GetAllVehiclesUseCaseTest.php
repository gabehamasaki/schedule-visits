<?php

namespace Tests\Unit\Application\UseCases;

use App\Application\DTOs\VehicleResponseDTO;
use App\Application\UseCases\GetAllVehiclesUseCase;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetAllVehiclesUseCaseTest extends TestCase
{
    public function testItReturnsAllVehicles(): void
    {
        $vehicle1 = new Vehicle(1, 'Toyota', 'Corolla', '2020', 20000.0, 'New York', 'url1');
        $vehicle2 = new Vehicle(2, 'Honda', 'Civic', '2021', 22000.0, 'Los Angeles', 'url2');

        $repositoryMock = $this->createMock(VehicleRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$vehicle1, $vehicle2]);

        $useCase = new GetAllVehiclesUseCase($repositoryMock);

        $response = $useCase->execute();

        $this->assertCount(2, $response);
        $this->assertInstanceOf(VehicleResponseDTO::class, $response[0]);
        $this->assertInstanceOf(VehicleResponseDTO::class, $response[1]);
    }
}
