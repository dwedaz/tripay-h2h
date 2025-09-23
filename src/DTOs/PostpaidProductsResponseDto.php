<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidProductsResponseDto
{
    /**
     * @param bool $success
     * @param string $message
     * @param PostpaidProductDto[] $data
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
                $data[] = PostpaidProductDto::fromArray($item);
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
            'data' => array_map(fn(PostpaidProductDto $product) => $product->toArray(), $this->data),
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
     * @return PostpaidProductDto[]
     */
    public function getProducts(): array
    {
        return $this->data;
    }

    /**
     * Get available products
     * 
     * @return PostpaidProductDto[]
     */
    public function getAvailableProducts(): array
    {
        return array_filter($this->data, fn(PostpaidProductDto $product) => $product->isAvailable());
    }

    /**
     * Find product by ID
     */
    public function findProductById(string $id): ?PostpaidProductDto
    {
        foreach ($this->data as $product) {
            if ($product->id === $id) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Find product by code
     */
    public function findProductByCode(string $code): ?PostpaidProductDto
    {
        foreach ($this->data as $product) {
            if (strtoupper($product->code) === strtoupper($code)) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Find product by name (partial search)
     */
    public function findProductByName(string $name): ?PostpaidProductDto
    {
        foreach ($this->data as $product) {
            if (stripos($product->name, $name) !== false) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Get first product
     */
    public function getFirstProduct(): ?PostpaidProductDto
    {
        return $this->data[0] ?? null;
    }

    /**
     * Get products by operator ID
     * 
     * @return PostpaidProductDto[]
     */
    public function getProductsByOperatorId(string $operatorId): array
    {
        return array_filter($this->data, fn(PostpaidProductDto $product) => $product->operatorId === $operatorId);
    }

    /**
     * Get products by category ID
     * 
     * @return PostpaidProductDto[]
     */
    public function getProductsByCategoryId(string $categoryId): array
    {
        return array_filter($this->data, fn(PostpaidProductDto $product) => $product->categoryId === $categoryId);
    }

    /**
     * Get products by availability status
     * 
     * @param bool $available
     * @return PostpaidProductDto[]
     */
    public function getProductsByAvailability(bool $available): array
    {
        return array_filter($this->data, function(PostpaidProductDto $product) use ($available) {
            return $product->isAvailable() === $available;
        });
    }

    /**
     * Check if any products are available
     */
    public function hasAvailableProducts(): bool
    {
        foreach ($this->data as $product) {
            if ($product->isAvailable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get count of available products
     */
    public function countAvailableProducts(): int
    {
        return count($this->getAvailableProducts());
    }

    /**
     * Get products with admin fee
     * 
     * @return PostpaidProductDto[]
     */
    public function getProductsWithAdminFee(): array
    {
        return array_filter($this->data, fn(PostpaidProductDto $product) => $product->hasAdminFee());
    }

    /**
     * Get products without admin fee
     * 
     * @return PostpaidProductDto[]
     */
    public function getProductsWithoutAdminFee(): array
    {
        return array_filter($this->data, fn(PostpaidProductDto $product) => !$product->hasAdminFee());
    }

    /**
     * Get products sorted by admin fee (ascending)
     * 
     * @return PostpaidProductDto[]
     */
    public function getProductsSortedByAdminFee(): array
    {
        $products = $this->data;
        usort($products, fn(PostpaidProductDto $a, PostpaidProductDto $b) => 
            $a->getAdminFeeAsInt() <=> $b->getAdminFeeAsInt()
        );
        return $products;
    }

    /**
     * Get unique operator IDs
     * 
     * @return string[]
     */
    public function getUniqueOperatorIds(): array
    {
        $operatorIds = array_map(fn(PostpaidProductDto $product) => $product->operatorId, $this->data);
        return array_unique($operatorIds);
    }

    /**
     * Get unique category IDs
     * 
     * @return string[]
     */
    public function getUniqueCategoryIds(): array
    {
        $categoryIds = array_map(fn(PostpaidProductDto $product) => $product->categoryId, $this->data);
        return array_unique($categoryIds);
    }
}
