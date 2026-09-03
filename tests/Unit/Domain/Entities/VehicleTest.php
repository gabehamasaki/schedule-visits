<?php

namespace Tests\Unit\Domain\Entities;

use PHPUnit\Framework\TestCase;
use App\Domain\Entities\Vehicle;

class VehicleTest extends TestCase
{
    public function testVehicleCanBeInstantiatedAndReturnsCorrectValues(): void
    {
        $vehicle = new Vehicle(
            id: 1,
            brand: 'Porsche',
            model: '911',
            version: 'Carrera',
            price: 850000.00,
            location: 'São Paulo',
            imageUrl: 'http://example.com/porsche.jpg',
        );

        $this->assertEquals(1, $vehicle->getId());
        $this->assertEquals('Porsche', $vehicle->getBrand());
        $this->assertEquals(850000.00, $vehicle->getPrice());

        $arrayData = $vehicle->toArray();
        $this->assertArrayHasKey('model', $arrayData);
        $this->assertEquals('911', $arrayData['model']);
    }
}
