<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidProductDto
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
            id: (string) ($data['id'] ?? ''),
            code: (string) ($data['code'] ?? ''),
            operatorId: (string) ($data['pembelianoperator_id'] ?? ''),
            categoryId: (string) ($data['pembeliankategori_id'] ?? ''),
            name: (string) ($data['product_name'] ?? ''),
            price: (string) ($data['price'] ?? '0'),
            description: (string) ($data['desc'] ?? ''),
            status: (string) ($data['status'] ?? '0')
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
            'pembelianoperator_id' => $this->operatorId,
            'pembeliankategori_id' => $this->categoryId,
            'product_name' => $this->name,
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
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }
}