<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidCategoriesResponseDto
{
    /**
     * @param bool $success
     * @param string $message
     * @param PostpaidCategoryDto[] $data
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
        $success = (bool) ($response['success'] ?? false);
        $message = (string) ($response['message'] ?? '');
        $data = [];

        if ($success && isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $item) {
                $data[] = PostpaidCategoryDto::fromArray($item);
            }
        }

        return new self(
            success: $success,
            message: $message,
            data: $data
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
            'data' => array_map(fn(PostpaidCategoryDto $category) => $category->toArray(), $this->data),
        ];
    }

    /**
     * Check if response has error
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
     * Get count of categories
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get all categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getCategories(): array
    {
        return $this->data;
    }

    /**
     * Get available categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getAvailableCategories(): array
    {
        return array_filter($this->data, fn(PostpaidCategoryDto $category) => $category->isAvailable());
    }

    /**
     * Find category by ID
     */
    public function findCategoryById(string $id): ?PostpaidCategoryDto
    {
        foreach ($this->data as $category) {
            if ($category->id === $id) {
                return $category;
            }
        }
        return null;
    }

    /**
     * Get categories by type
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getCategoriesByType(string $type): array
    {
        return array_filter($this->data, fn(PostpaidCategoryDto $category) => $category->isType($type));
    }

    /**
     * Get available categories by type
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getAvailableCategoriesByType(string $type): array
    {
        return array_filter(
            $this->getAvailableCategories(), 
            fn(PostpaidCategoryDto $category) => $category->isType($type)
        );
    }

    /**
     * Get all unique available types
     * 
     * @return string[]
     */
    public function getAvailableTypes(): array
    {
        $types = [];
        foreach ($this->getAvailableCategories() as $category) {
            if (!in_array($category->type, $types)) {
                $types[] = $category->type;
            }
        }
        return $types;
    }

    /**
     * Get PLN categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getPlnCategories(): array
    {
        return $this->getCategoriesByType('PLN');
    }

    /**
     * Get BPJS categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getBpjsCategories(): array
    {
        return $this->getCategoriesByType('BPJS');
    }

    /**
     * Get ASURANSI categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getAsuransiCategories(): array
    {
        return $this->getCategoriesByType('ASURANSI');
    }

    /**
     * Get TV categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getTvCategories(): array
    {
        return $this->getCategoriesByType('TV');
    }

    /**
     * Get INTERNET categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getInternetCategories(): array
    {
        return $this->getCategoriesByType('INTERNET');
    }

    /**
     * Get MULTIFINANCE categories
     * 
     * @return PostpaidCategoryDto[]
     */
    public function getMultifinanceCategories(): array
    {
        return $this->getCategoriesByType('MULTIFINANCE');
    }
}