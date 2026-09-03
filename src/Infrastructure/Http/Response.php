<?php

namespace App\Infrastructure\Http;

use JsonSerializable;

class Response implements JsonSerializable
{
    /**
     * @param int $statusCode
     * @param string $status
     * @param string|null $message
     * @param mixed $data
     * @param array<string, mixed> $errors
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly string $status,
        public readonly ?string $message = null,
        public readonly mixed $data = null,
        public readonly array $errors = [],
    ) {}

    /**
     * @param mixed $data
     * @param string|null $message
     */
    public static function success(mixed $data = null, ?string $message = null): self
    {
        return new self(200, 'success', $message, $data);
    }

    /**
     * @param mixed $data
     * @param string|null $message
     */
    public static function created(mixed $data = null, ?string $message = null): self
    {
        return new self(201, 'success', $message, $data);
    }

    /**
     * @param int $statusCode
     * @param string $message
     * @param array<string, mixed> $errors
     */
    public static function error(int $statusCode, string $message, array $errors = []): self
    {
        return new self($statusCode, 'error', $message, null, $errors);
    }

    /**
     * @param string $message
     */
    public static function notFound(string $message = 'Resource not found.'): self
    {
        return new self(404, 'error', $message);
    }

    /**
     * @param string $message
     */
    public static function conflict(string $message = 'Resource conflict.'): self
    {
        return new self(409, 'error', $message);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $errors
     */
    public static function badRequest(string $message = 'Bad request.', array $errors = []): self
    {
        return new self(400, 'error', $message, null, $errors);
    }

    /**
     * @param array<string> $allowedMethods
     */
    public static function methodNotAllowed(array $allowedMethods): self
    {
        return new self(405, 'error', 'Method Not Allowed', null, ['allowed_methods' => $allowedMethods]);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $response = ['status' => $this->status];

        if (!is_null($this->message)) {
            $response['message'] = $this->message;
        }

        if (!is_null($this->data)) {
            $response['data'] = $this->data;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        echo json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
