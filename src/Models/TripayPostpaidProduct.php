<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Models;

use Dwedaz\TripayH2H\DTOs\PostpaidProductDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripayPostpaidProduct extends Model
{
    protected $table = 'tripay_postpaid_products';

    protected $fillable = [
        'id',
        'code',
        'name',
        'admin_fee',
        'operator_id',
        'category_id',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'admin_fee' => 'float',
        'operator_id' => 'integer',
        'category_id' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Create model from DTO
     */
    public static function fromDto(PostpaidProductDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'code' => $dto->code,
            'name' => $dto->name,
            'admin_fee' => (float) $dto->adminFee,
            'operator_id' => (int) $dto->operatorId,
            'category_id' => (int) $dto->categoryId,
            'status' => $dto->status,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PostpaidProductDto
    {
        return new PostpaidProductDto(
            id: (string) $this->id,
            code: $this->code,
            name: $this->name,
            adminFee: (string) $this->admin_fee,
            operatorId: (string) $this->operator_id,
            categoryId: (string) $this->category_id,
            status: $this->status
        );
    }

    /**
     * Get the category that owns this product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TripayPostpaidCategory::class, 'category_id', 'id');
    }

    /**
     * Get the operator that owns this product
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(TripayPostpaidOperator::class, 'operator_id', 'id');
    }

    /**
     * Scope to get available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Scope to get unavailable products
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', '0');
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by operator
     */
    public function scopeByOperator($query, $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }

    /**
     * Scope to filter by code
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Check if product is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if product is unavailable
     */
    public function isUnavailable(): bool
    {
        return $this->status === 0;
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
        return 'Rp ' . number_format((float) $this->admin_fee, 0, ',', '.');
    }

    /**
     * Find by ID
     */
    public static function findById(int $id): ?self
    {
        return static::where('id', $id)->first();
    }

    /**
     * Find by code
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }

    /**
     * Create or update from DTO
     */
    public static function createOrUpdateFromDto(PostpaidProductDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'code' => $dto->code,
                'name' => $dto->name,
                'admin_fee' => (float) $dto->adminFee,
                'operator_id' => (int) $dto->operatorId,
                'category_id' => (int) $dto->categoryId,
                'status' => $dto->status,
            ]
        );
    }

    /**
     * Sync products from DTOs array
     */
    public static function syncFromDtos(array $dtos): void
    {
        foreach ($dtos as $dto) {
            static::createOrUpdateFromDto($dto);
        }
    }
}