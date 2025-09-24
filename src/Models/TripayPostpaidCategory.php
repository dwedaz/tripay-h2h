<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Models;

use Dwedaz\TripayH2H\DTOs\PostpaidCategoryDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripayPostpaidCategory extends Model
{
    use Backpack\CRUD\app\Models\Traits\CrudTrait;

    protected $table = 'tripay_postpaid_categories';

    protected $fillable = [
        'id',
        'name',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'status' => 'boolean',
    ];

    /**
     * Create model from DTO
     */
    public static function fromDto(PostpaidCategoryDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'name' => $dto->name,
            'status' => $dto->status,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PostpaidCategoryDto
    {
        return new PostpaidCategoryDto(
            id: (string) $this->id,
            name: $this->name,
            status: $this->status
        );
    }

    /**
     * Get operators for this category
     */
    public function operators(): HasMany
    {
        return $this->hasMany(TripayPostpaidOperator::class, 'category_id', 'id');
    }

    /**
     * Get all products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(TripayPostpaidProduct::class, 'category_id', 'id');
    }

    /**
     * Scope to get available categories
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Scope to get unavailable categories
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', '0');
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
     * Find by ID
     */
    public static function findById(int $id): ?self
    {
        return static::where('id', $id)->first();
    }

    /**
     * Create or update from DTO
     */
    public static function createOrUpdateFromDto(PostpaidCategoryDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'name' => $dto->name,
                'status' => $dto->status,
            ]
        );
    }

    /**
     * Sync categories from DTOs array
     */
    public static function syncFromDtos(array $dtos): void
    {
        foreach ($dtos as $dto) {
            static::createOrUpdateFromDto($dto);
        }
    }
}