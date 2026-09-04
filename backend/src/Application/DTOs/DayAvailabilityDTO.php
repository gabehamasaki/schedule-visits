<?php

namespace App\Application\DTOs;

class DayAvailabilityDTO implements \JsonSerializable
{
    /**
     * @param SlotDTO[] $slots Every slot the schedule offers for this date
     */
    public function __construct(
        public readonly string $date,
        public readonly array $slots,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date,
            'slots' => $this->slots,
        ];
    }
}
