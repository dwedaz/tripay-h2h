<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidOperatorDto
{
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public string $categoryId,
        public string $status,
        public string $image,
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            code: (string) ($data['product_id'] ?? ''),
            name: (string) ($data['product_name'] ?? ''),
            categoryId: (string) ($data['pembeliankategori_id'] ?? ''),
            status: (string) ($data['status'] ?? '0'),
            image: (string) $data['img'] ?? '',
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
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'image' => $this->image,
        ];
    }

    /**
     * Check if operator is available
     */
    public function isAvailable(): bool
    {
        return $this->status === '1';
    }
}