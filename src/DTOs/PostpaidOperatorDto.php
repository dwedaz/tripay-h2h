<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidOperatorDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $status,
        public ?int $categoryId
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $data): self
    {
        
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['product_name'] ?? ''),
            status: (string) ($data['status'] ?? '0'),
            categoryId: (int) $data['pembayarankategori_id'] ?? null, 
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'status' => $this->status,
        ];
    }

    /**
     * Check if operator is available
     */
    public function isAvailable(): bool
    {
        return $this->status === '1';
    }

    /**
     * Check if operator is unavailable
     */
    public function isUnavailable(): bool
    {
        return $this->status === '0';
    }

    /**
     * Get status as boolean
     */
    public function getStatusBoolean(): bool
    {
        return $this->isAvailable();
    }

    /**
     * Get formatted status text
     */
    public function getStatusText(): string
    {
        return $this->isAvailable() ? 'Available' : 'Unavailable';
    }
}
