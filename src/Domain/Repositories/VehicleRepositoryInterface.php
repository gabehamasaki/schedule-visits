<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Vehicle;

interface VehicleRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?Vehicle;
}
