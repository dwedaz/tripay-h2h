<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidProductsResponseDto
{
    /**
     * @param bool $success
     * @param string $message
     * @param PrepaidProductDto[] $data
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
                $data[] = PrepaidProductDto::fromArray($item);
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
            'data' => array_map(fn(PrepaidProductDto $product) => $product->toArray(), $this->data),
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
     * Get count of products
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get all products
     * 
     * @return PrepaidProductDto[]
     */
    public function getProducts(): array
    {
        return $this->data;
    }

    /**
     * Get available products
     * 
     * @return PrepaidProductDto[]
     */
    public function getAvailableProducts(): array
    {
        return array_filter($this->data, fn(PrepaidProductDto $product) => $product->isAvailable());
    }

    /**
     * Find product by ID
     */
    public function findProductById(string $id): ?PrepaidProductDto
    {
        foreach ($this->data as $product) {
            if ($product->id === $id) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Find product by product ID
     */
    public function findProductByProductId(string $productId): ?PrepaidProductDto
    {
        foreach ($this->data as $product) {
            if ($product->productId === $productId) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Get products by operator ID
     * 
     * @return PrepaidProductDto[]
     */
    public function getProductsByOperatorId(string $operatorId): array
    {
        return array_filter($this->data, fn(PrepaidProductDto $product) => $product->operatorId === $operatorId);
    }

    /**
     * Get products by category ID
     * 
     * @return PrepaidProductDto[]
     */
    public function getProductsByCategoryId(string $categoryId): array
    {
        return array_filter($this->data, fn(PrepaidProductDto $product) => $product->categoryId === $categoryId);
    }
}