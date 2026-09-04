<?php

namespace App\Domain\ValueObjects;

class BusinessHours
{
    /**
     * @param array<string> $slots
     */
    private function __construct(
        private readonly array $slots,
    ) {}

    /**
     * @param string $firstSlot First slot in HH:MM format
     * @param string $lastSlot Last slot in HH:MM format
     * @param int $slotDuration Duration of each slot in minutes
     */
    public static function fromRange(string $firstSlot, string $lastSlot, int $slotDuration): self
    {
        if ($slotDuration <= 0) {
            throw new \InvalidArgumentException('Slot duration must be greater than zero.');
        }

        $start = self::parse($firstSlot);
        $end = self::parse($lastSlot);

        if ($start >= $end) {
            throw new \InvalidArgumentException('First slot must be before last slot.');
        }

        $slots = [];
        $interval = new \DateInterval("PT{$slotDuration}M");

        for ($current = $start; $current <= $end; $current = $current->add($interval)) {
            $slots[] = $current->format('H:i');
        }

        return new self($slots);
    }

    /**
     * @param array<string> $slots
     */
    public static function fromList(array $slots): self
    {
        $normalized = array_map(fn($slot) => self::parse($slot)->format('H:i'), $slots);

        sort($normalized);

        return new self($normalized);
    }

    /**
     * @return array<string>
     */
    public function slots(): array
    {
        return $this->slots;
    }

    public function contains(string $slot): bool
    {
        return in_array($slot, $this->slots, true);
    }

    private static function parse(string $time): \DateTimeImmutable
    {
        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time) !== 1) {
            throw new \InvalidArgumentException("Invalid time \"$time\". Expected HH:MM.");
        }

        $parsed = \DateTimeImmutable::createFromFormat('!H:i', $time);

        if ($parsed === false) {
            throw new \InvalidArgumentException("Invalid time \"$time\". Expected HH:MM.");
        }

        return $parsed;
    }
}
