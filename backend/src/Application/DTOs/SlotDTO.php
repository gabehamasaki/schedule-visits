<?php

namespace App\Application\DTOs;

class SlotDTO implements \JsonSerializable
{
    public function __construct(
        public readonly string $time,
        public readonly bool $available,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'time' => $this->time,
            'available' => $this->available,
        ];
    }
}
