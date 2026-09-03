<?php

namespace App\Infrastructure\Http;

class Request
{
    /**
     * @param string $method
     * @param string $uri
     * @param array<string, string> $params
     * @param array<string, mixed>  $query
     * @param array<string, mixed>  $body
     */
    public function __construct(
        public readonly string $method,
        public readonly string $uri,
        public readonly array $params,
        public readonly array $query,
        public readonly array $body,
    ) {}

    public function query(string $key, ?string $default = null): ?string
    {
        return $this->scalar($this->query, $key) ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function param(string $key, ?string $default = null): ?string
    {
        return $this->scalar($this->params, $key) ?? $default;
    }

    public function paramInt(string $key, ?int $default = null): ?int
    {
        $value = $this->scalar($this->params, $key);
        return is_numeric($value) ? (int) $value : $default;
    }

    /** @param array<string, mixed> $source */
    private function scalar(array $source, string $key): ?string
    {
        $value = $source[$key] ?? null;
        return is_scalar($value) ? (string) $value : null;
    }

    /** @param array<string, string> $params */
    public static function fromGlobals(array $params = []): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $query = $_GET;
        $body = [];

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw === false ? '' : $raw, true);
            $body = is_array($decoded) ? $decoded : [];
        }

        return new self($method, $uri, $params, $query, $body);
    }
}
