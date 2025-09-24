<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Models;

use Dwedaz\TripayH2H\DTOs\PostpaidOperatorDto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripayPostpaidOperator extends Model
{
    use Backpack\CRUD\app\Models\Traits\CrudTrait;
    
    protected $table = 'tripay_postpaid_operators';

    protected $fillable = [
        'id',
        'name',
        'status',
        'category_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'status' => 'boolean',
        'category_id' => 'integer',
    ];

    /**
     * Create model from DTO
     */
    public static function fromDto(PostpaidOperatorDto $dto): self
    {
        return new self([
            'id' => (int) $dto->id,
            'name' => $dto->name,
            'status' => $dto->status,
            'category_id' => (int) $dto->categoryId,
        ]);
    }

    /**
     * Convert model to DTO
     */
    public function toDto(): PostpaidOperatorDto
    {
        return new PostpaidOperatorDto(
            id: (string) $this->id,
            name: $this->name,
            status: $this->status,
            categoryId: (int) $this->category_id
        );
    }

    /**
     * Get the category that owns this operator
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TripayPostpaidCategory::class, 'category_id', 'id');
    }

    /**
     * Get all products for this operator
     */
    public function products(): HasMany
    {
        return $this->hasMany(TripayPostpaidProduct::class, 'operator_id', 'id');
    }

    /**
     * Scope to get available operators
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get unavailable operators
     */
    public function scopeUnavailable($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
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
     * Find by ID
     */
    public static function findById(int $id): ?self
    {
        return static::where('id', $id)->first();
    }

    /**
     * Create or update from DTO
     */
    public static function createOrUpdateFromDto(PostpaidOperatorDto $dto): self
    {
        return static::updateOrCreate(
            ['id' => (int) $dto->id],
            [
                'name' => $dto->name,
                'status' => $dto->status,
                'category_id' => (int) $dto->categoryId,
            ]
        );
    }

    /**
     * Sync operators from DTOs array
     */
    public static function syncFromDtos(array $dtos): void
    {
        foreach ($dtos as $dto) {
            static::createOrUpdateFromDto($dto);
        }
    }
}