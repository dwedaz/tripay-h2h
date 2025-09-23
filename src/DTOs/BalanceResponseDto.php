<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class BalanceResponseDto
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
     * Get balance amount in rupiah
     */
    public function getBalance(): ?int
    {
        return $this->data;
    }

    /**
     * Get formatted balance with currency
     */
    public function getFormattedBalance(): string
    {
        if ($this->data === null) {
            return 'Rp. 0';
        }

        return 'Rp. ' . number_format($this->data, 0, ',', '.');
    }

    /**
     * Check if balance is sufficient for amount
     */
    public function isSufficientFor(int $amount): bool
    {
        return $this->success && $this->data !== null && $this->data >= $amount;
    }

    /**
     * Check if balance is available (success and has balance data)
     */
    public function hasBalance(): bool
    {
        return $this->success && $this->data !== null;
    }

    /**
     * Check if response indicates an error
     */
    public function hasError(): bool
    {
        return !$this->success;
    }

    /**
     * Check if balance is low (less than specified threshold)
     */
    public function isLowBalance(int $threshold = 100000): bool
    {
        return $this->hasBalance() && $this->data < $threshold;
    }
}