<?php

namespace Dwedaz\TripayH2H\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoryDto;

class TripayPrepaidCategory extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tripay_prepaid_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'status' => 'boolean',
    ];

    /**
     * Create model instance from DTO
     */
    public static function fromDto(PrepaidCategoryDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'name' => $dto->name,
            'type' => $dto->type,
            'status' => $dto->status,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PrepaidCategoryDto
    {
        return new PrepaidCategoryDto(
            id: (string) $this->id,
            name: $this->name,
            type: $this->type,
            status: $this->status
        );
    }

    /**
     * Get operators for this category
     */
    public function operators(): HasMany
    {
        return $this->hasMany(TripayPrepaidOperator::class, 'category_id', 'id');
    }

    /**
     * Get products for this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(TripayPrepaidProduct::class, 'category_id', 'id');
    }

    /**
     * Scope for available categories
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Scope for unavailable categories
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', '0');
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', strtoupper($type));
    }

    /**
     * Check if category is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if category is unavailable
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
     * Check if category is of specific type
     */
    public function isType(string $type): bool
    {
        return strtoupper($this->type) === strtoupper($type);
    }

    /**
     * Check if this is a pulsa category
     */
    public function isPulsa(): bool
    {
        return $this->isType('PULSA');
    }

    /**
     * Check if this is a game category
     */
    public function isGame(): bool
    {
        return $this->isType('GAME');
    }

    /**
     * Check if this is a PLN category
     */
    public function isPln(): bool
    {
        return $this->isType('PLN');
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
    public static function createOrUpdateFromDto(PrepaidCategoryDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'name' => $dto->name,
                'type' => $dto->type,
                'status' => $dto->status,
            ]
        );
    }
}