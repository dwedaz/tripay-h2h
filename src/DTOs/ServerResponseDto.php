<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class ServerResponseDto
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?int $data = null
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $response): self
    {
        return new self(
            success: $response['success'] ?? false,
            message: $response['message'] ?? '',
            data: $response['data'] ?? null
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    /**
     * Check if server is online
     */
    public function isServerOnline(): bool
    {
        return $this->success && $this->data === 1;
    }

    /**
     * Check if response indicates an error
     */
    public function hasError(): bool
    {
        return !$this->success;
    }
}