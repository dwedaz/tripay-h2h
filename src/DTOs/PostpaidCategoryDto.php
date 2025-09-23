<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidCategoryDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $status
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $data): self
    {
        
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
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
            'product_name' => $this->name,
            'status' => $this->status,
        ];
    }

    /**
     * Check if category is available
     */
    public function isAvailable(): bool
    {
        return $this->status === '1';
    }

    /**
     * Check if category is unavailable
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

    /**
     * Check if category is of specific type
     */
    public function isType(string $type): bool
    {
        return strtoupper($this->type) === strtoupper($type);
    }

    /**
     * Check if this is a PLN category
     */
    public function isPln(): bool
    {
        return $this->isType('PLN');
    }

    /**
     * Check if this is a BPJS category
     */
    public function isBpjs(): bool
    {
        return $this->isType('BPJS');
    }

    /**
     * Check if this is an ASURANSI category
     */
    public function isAsuransi(): bool
    {
        return $this->isType('ASURANSI');
    }

    /**
     * Check if this is a TV category
     */
    public function isTv(): bool
    {
        return $this->isType('TV');
    }

    /**
     * Check if this is an Internet category
     */
    public function isInternet(): bool
    {
        return $this->isType('INTERNET');
    }

    /**
     * Check if this is a Multifinance category
     */
    public function isMultifinance(): bool
    {
        return $this->isType('MULTIFINANCE');
    }
}