<?php

namespace Tests\Unit\Application\DTOs;

use App\Application\DTOs\ScheduleVisitDTO;
use App\Domain\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ScheduleVisitDTOTest extends TestCase
{
    public function testValidateReturnsSelfWhenDataIsValid(): void
    {
        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11987654321', '2026-09-10', '10:00');

        $this->assertSame($dto, $dto->validate());
    }

    public function testValidateThrowsForEmptyName(): void
    {
        $dto = new ScheduleVisitDTO(1, '', 'test@test.com', '11987654321', '2026-09-10', '10:00');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->getDetails());
        }
    }

    public function testValidateThrowsForInvalidEmail(): void
    {
        $dto = new ScheduleVisitDTO(1, 'Test', 'not-an-email', '11987654321', '2026-09-10', '10:00');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->getDetails());
        }
    }

    public function testValidateThrowsForInvalidPhone(): void
    {
        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '123', '2026-09-10', '10:00');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('phone', $e->getDetails());
        }
    }

    public function testValidateThrowsForInvalidDate(): void
    {
        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11987654321', '2026-13-40', '10:00');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('date', $e->getDetails());
        }
    }

    public function testValidateThrowsForInvalidTime(): void
    {
        $dto = new ScheduleVisitDTO(1, 'Test', 'test@test.com', '11987654321', '2026-09-10', '25:99');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('time', $e->getDetails());
        }
    }

    public function testValidateAggregatesAllFieldErrors(): void
    {
        $dto = new ScheduleVisitDTO(1, '', 'not-an-email', '1', 'invalid-date', '99:99');

        try {
            $dto->validate();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $e) {
            $errors = $e->getDetails();
            $this->assertCount(5, $errors);
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('phone', $errors);
            $this->assertArrayHasKey('date', $errors);
            $this->assertArrayHasKey('time', $errors);
        }
    }
}
