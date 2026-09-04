<?php

namespace App\Application\DTOs;

class VehicleResponseDTO implements \JsonSerializable
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'model' => $this->model,
            'version' => $this->version,
            'price' => $this->price,
            'location' => $this->location,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
