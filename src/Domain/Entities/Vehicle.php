<?php

namespace App\Domain\Entities;

class Vehicle
{
    public function __construct(
        private int $id,
        private string $brand,
        private string $model,
        private string $version,
        private float $price,
        private string $location,
        private string $imageUrl,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
    public function getBrand(): string
    {
        return $this->brand;
    }
    public function getModel(): string
    {
        return $this->model;
    }
    public function getVersion(): string
    {
        return $this->version;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
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
