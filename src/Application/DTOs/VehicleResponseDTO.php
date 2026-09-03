<?php

namespace App\Application\DTOs;

class VehicleResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $brand,
        public readonly string $model,
        public readonly string $version,
        public readonly float $price,
        public readonly string $location,
        public readonly string $imageUrl,
    ) {}
}
