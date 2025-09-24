<?php

namespace Dwedaz\TripayH2H\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDto;

class TripayPrepaidProduct extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tripay_prepaid_products';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'code',
        'operator_id',
        'category_id',
        'name',
        'price',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'operator_id' => 'integer',
        'category_id' => 'integer',
        'name' => 'string',
        'price' => 'decimal:2',
        'description' => 'string',
        'status' => 'boolean',
    ];

    /**
     * Create model instance from DTO
     */
    public static function fromDto(PrepaidProductDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'code' => $dto->code,
            'operator_id' => (int) $dto->operatorId,
            'category_id' => (int) $dto->categoryId,
            'name' => $dto->name,
            'price' => (float) $dto->price,
            'description' => $dto->description,
            'status' => $dto->status,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PrepaidProductDto
    {
        return new PrepaidProductDto(
            id: (string) $this->id,
            code: $this->code,
            operatorId: (string) $this->operator_id,
            categoryId: (string) $this->category_id,
            name: $this->name,
            price: (string) $this->price,
            description: $this->description,
            status: $this->status
        );
    }

    /**
     * Get the category that owns this product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TripayPrepaidCategory::class, 'category_id', 'id');
    }

    /**
     * Get the operator that owns this product
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(TripayPrepaidOperator::class, 'operator_id', 'id');
    }

    /**
     * Scope for available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Scope for unavailable products
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', '0');
    }

    /**
     * Scope for specific category
     */
    public function scopeForCategory($query, string $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for specific operator
     */
    public function scopeForOperator($query, string $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }

    /**
     * Scope for specific code
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope for price range
     */
    public function scopePriceBetween($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope for minimum price
     */
    public function scopeMinPrice($query, float $minPrice)
    {
        return $query->where('price', '>=', $minPrice);
    }

    /**
     * Scope for maximum price
     */
    public function scopeMaxPrice($query, float $maxPrice)
    {
        return $query->where('price', '<=', $maxPrice);
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
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Get price as float
     */
    public function getPriceFloat(): float
    {
        return (float) $this->price;
    }

    /**
     * Check if product is expensive (above certain threshold)
     */
    public function isExpensive(float $threshold = 100000): bool
    {
        return $this->getPriceFloat() > $threshold;
    }

    /**
     * Check if product is cheap (below certain threshold)
     */
    public function isCheap(float $threshold = 10000): bool
    {
        return $this->getPriceFloat() < $threshold;
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
    public static function createOrUpdateFromDto(PrepaidProductDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'code' => $dto->code,
                'operator_id' => (int) $dto->operatorId,
                'category_id' => (int) $dto->categoryId,
                'name' => $dto->name,
                'price' => (float) $dto->price,
                'description' => $dto->description,
                'status' => $dto->status,
            ]
        );
    }
}