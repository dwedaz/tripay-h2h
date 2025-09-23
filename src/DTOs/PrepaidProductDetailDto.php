<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidProductDetailDto
{
    public function __construct(
        public string $id,
        public string $code,
        public string $operatorId,
        public string $categoryId,
        public string $name,
        public string $price,
        public string $description,
        public string $status
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $data): self
    {

        return new self(
            id: (string) ($data['id'] ?? null),
            code: (string) ($data['code'] ?? null),
            operatorId: (string) ($data['pembelianoperator_id'] ?? null),
            categoryId: (string) ($data['pembeliankategori_id'] ?? null),
            name: (string) ($data['name'] ?? null),
            price: (string) ($data['price'] ?? 0),
            description: (string) ($data['description'] ?? null),
            status: (string) ($data['status'] ?? 0)
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'operatorId' => $this->operatorId,
            'categoryId' => $this->categoryId,
            'name' => $this->name,
            'price' => $this->price,
            'desc' => $this->description,
            'status' => $this->status,
        ];
    }

    /**
     * Check if product is available
     */
    public function isAvailable(): bool
    {
        return $this->status === '1';
    }

    /**
     * Check if product is unavailable
     */
    public function isUnavailable(): bool
    {
        return $this->status === '0';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
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