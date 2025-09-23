<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidProductDto
{
    public function __construct(
        public string $id,
        public string $code,
        public string $name,
        public string $adminFee,
        public string $operatorId,
        public string $categoryId,
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
            name: (string) ($data['product_name'] ?? ''),
            adminFee: (string) ($data['biaya_admin'] ?? '0'),
            operatorId: (string) ($data['pembayaranoperator_id'] ?? ''),
            categoryId: (string) ($data['pembayarankategori_id'] ?? ''),
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
            'product_name' => $this->name,
            'biaya_admin' => $this->adminFee,
            'operator_id' => $this->operatorId,
            'category_id' => $this->categoryId,
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
     * Get formatted admin fee
     */
    public function getFormattedAdminFee(): string
    {
        return 'Rp ' . number_format((float) $this->adminFee, 0, ',', '.');
    }

    /**
     * Get admin fee as integer
     */
    public function getAdminFeeAsInt(): int
    {
        return (int) $this->adminFee;
    }

    /**
     * Check if product has admin fee
     */
    public function hasAdminFee(): bool
    {
        return $this->getAdminFeeAsInt() > 0;
    }

    /**
     * Get admin fee in rupiah format without "Rp" prefix
     */
    public function getAdminFeeFormatted(): string
    {
        return number_format((float) $this->adminFee, 0, ',', '.');
    }
}
