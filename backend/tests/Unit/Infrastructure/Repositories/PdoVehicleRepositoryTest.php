<?php

namespace Tests\Unit\Infrastructure\Repositories;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Repositories\PdoVehicleRepository;
use App\Domain\Entities\Vehicle;
use PDO;
use PDOStatement;

class PdoVehicleRepositoryTest extends TestCase
{
    public function testFindByIdReturnsVehicleWhenFound(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        // Expects query preparation
        $pdoMock->expects($this->once())
            ->method('prepare')
            ->with("SELECT * FROM vehicles WHERE id = :id")
            ->willReturn($stmtMock);

        // Expects execution and fetching data
        $stmtMock->expects($this->once())->method('execute')->with(['id' => 1]);
        $stmtMock->expects($this->once())->method('fetch')->willReturn([
            'id' => 1,
            'brand' => 'Audi',
            'model' => 'A3',
            'version' => '1.4 TFSI',
            'price' => 120000.00,
            'location' => 'SP',
            'image_url' => 'img.jpg',
        ]);

        $repository = new PdoVehicleRepository($pdoMock);
        $vehicle = $repository->findById(1);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertEquals('Audi', $vehicle->getBrand());
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        $pdoMock->method('prepare')->willReturn($stmtMock);
        $stmtMock->method('fetch')->willReturn(false); // No data found

        $repository = new PdoVehicleRepository($pdoMock);
        $vehicle = $repository->findById(999);

        $this->assertNull($vehicle);
    }
}
