<?php

namespace App\Infrastructure\Http\DTOs;

use JsonSerializable;

class ApiResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $status,
        public readonly mixed $data = null,
        public readonly ?string $message = null
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $response = ['status' => $this->status];

        if ($this->message !== null) {
            $response['message'] = $this->message;
        }

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        return $response;
    }
}
