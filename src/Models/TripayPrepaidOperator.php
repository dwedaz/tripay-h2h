<?php

namespace Dwedaz\TripayH2H\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorDto;

class TripayPrepaidOperator extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tripay_prepaid_operators';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'code',
        'name',
        'category_id',
        'status',
        'image',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string',
        'category_id' => 'integer',
        'status' => 'boolean',
        'image' => 'string',
    ];

    /**
     * Create model instance from DTO
     */
    public static function fromDto(PrepaidOperatorDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'code' => $dto->code,
            'name' => $dto->name,
            'category_id' => (int) $dto->categoryId,
            'status' => $dto->status,
            'image' => $dto->image,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PrepaidOperatorDto
    {
        return new PrepaidOperatorDto(
            id: (string) $this->id,
            code: $this->code,
            name: $this->name,
            categoryId: (string) $this->category_id,
            status: $this->status,
            image: $this->image
        );
    }

    /**
     * Get the category that owns this operator
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TripayPrepaidCategory::class, 'category_id', 'id');
    }

    /**
     * Get products for this operator
     */
    public function products(): HasMany
    {
        return $this->hasMany(TripayPrepaidProduct::class, 'operator_id', 'id');
    }

    /**
     * Scope for available operators
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for unavailable operators
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope for specific category
     */
    public function scopeForCategory($query, string $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for specific code
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
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
     * Get formatted status text
     */
    public function getStatusText(): string
    {
        return $this->isAvailable() ? 'Available' : 'Unavailable';
    }

    /**
     * Get image URL with fallback
     */
    public function getImageUrl(): string
    {
        return $this->image ?: '/images/operators/default.png';
    }

    /**
     * Find by ID
     */
    public static function findById(int $id): ?self
    {
        return static::where('id', $id)->first();
    }

    /**
     * Create or update from DTO
     */
    public static function createOrUpdateFromDto(PrepaidOperatorDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'code' => $dto->code,
                'name' => $dto->name,
                'category_id' => (int) $dto->categoryId,
                'status' => $dto->status,
                'image' => $dto->image,
            ]
        );
    }
}