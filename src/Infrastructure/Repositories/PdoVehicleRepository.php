<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use PDO;

class PdoVehicleRepository implements VehicleRepositoryInterface
{
    public function __construct(private PDO $connection) {}

    /**
     *
     * @return Vehicle[]
     */
    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM vehicles");

        if (!$stmt) {
            throw new \RuntimeException("Failed to execute query: " . implode(", ", $this->connection->errorInfo()));
        }

        $vehiclesData = $stmt->fetchAll();

        return array_map(function ($data) {
            return new Vehicle(
                id: (int) $data['id'],
                brand: $data['brand'],
                model: $data['model'],
                version: $data['version'],
                price: (float) $data['price'],
                location: $data['location'],
                imageUrl: $data['image_url']
            );
        }, $vehiclesData);
    }

    public function findById(int $id): ?Vehicle
    {
        $stmt = $this->connection->prepare("SELECT * FROM vehicles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Vehicle(
            id: (int) $data['id'],
            brand: $data['brand'],
            model: $data['model'],
            version: $data['version'],
            price: (float) $data['price'],
            location: $data['location'],
            imageUrl: $data['image_url']
        );
    }
}
