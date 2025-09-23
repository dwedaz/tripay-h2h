<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidCategoriesResponseDto
{
    /**
     * @param PrepaidCategoryDto[] $data
     */
    public function __construct(
        public bool $success,
        public string $message,
        public array $data
    ) {}

    /**
     * Create DTO from API response array
     */
    public static function fromArray(array $response): self
    {
        $categories = [];
        
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $categoryData) {
                $categories[] = PrepaidCategoryDto::fromArray($categoryData);
            }
        }

        return new self(
            success: $response['success'] ?? false,
            message: $response['message'] ?? '',
            data: $categories
        );
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => array_map(fn(PrepaidCategoryDto $category) => $category->toArray(), $this->data),
        ];
    }

    /**
     * Check if response indicates an error
     */
    public function hasError(): bool
    {
        return !$this->success;
    }

    /**
     * Check if response has data
     */
    public function hasData(): bool
    {
        return $this->success && !empty($this->data);
    }

    /**
     * Get total number of categories
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get categories as array
     */
    public function getCategories(): array
    {
        return $this->data;
    }

    /**
     * Get only available categories
     */
    public function getAvailableCategories(): array
    {
        return array_filter($this->data, fn(PrepaidCategoryDto $category) => $category->isAvailable());
    }

    /**
     * Get categories by type
     */
    public function getCategoriesByType(string $type): array
    {
        return array_filter($this->data, fn(PrepaidCategoryDto $category) => $category->isType($type));
    }

    /**
     * Get available categories by type
     */
    public function getAvailableCategoriesByType(string $type): array
    {
        return array_filter(
            $this->data, 
            fn(PrepaidCategoryDto $category) => $category->isAvailable() && $category->isType($type)
        );
    }

    /**
     * Find category by ID
     */
    public function findCategoryById(string $id): ?PrepaidCategoryDto
    {
        foreach ($this->data as $category) {
            if ($category->id === $id) {
                return $category;
            }
        }
        return null;
    }

    /**
     * Get all available types
     */
    public function getAvailableTypes(): array
    {
        $types = [];
        foreach ($this->data as $category) {
            if ($category->isAvailable()) {
                $types[$category->type] = $category->type;
            }
        }
        return array_values($types);
    }

    /**
     * Get pulsa categories
     */
    public function getPulsaCategories(): array
    {
        return $this->getCategoriesByType('PULSA');
    }

    /**
     * Get game categories
     */
    public function getGameCategories(): array
    {
        return $this->getCategoriesByType('GAME');
    }

    /**
     * Get PLN categories
     */
    public function getPlnCategories(): array
    {
        return $this->getCategoriesByType('PLN');
    }
}