<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidProductDetailResponseDto
{
    /**
     * @param bool $success
     * @param string $message
     * @param PrepaidProductDetailDto[] $data
     */
    public function __construct(
        public bool $success,
        public string $message,
        public PostpaidProductDetailDto $data
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
           
                $data =  PostpaidProductDetailDto::fromArray($response['data']);
          
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
            'data' => $this->data instanceof PostpaidProductDetailDto ? $this->data->toArray() : []
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
     * Get all product details
     * 
     * @return PrepaidProductDetailDto[]
     */
    public function getProductDetails(): array
    {
        return $this->data;
    }

    /**
     * Get first product detail (usually there's only one)
     */
    public function getFirstProduct(): ?PostpaidProductDetailDto
    {
        return $this->data[0] ?? null;
    }

    /**
     * Find product by code
     */
    public function findProductByCode(string $code): ?PostpaidProductDetailDto
    {
        foreach ($this->data as $product) {
            if ($product->code === $code) {
                return $product instanceof PostpaidProductDetailDto ? $product : null;
            }
        }
        return null;
    }

    /**
     * Find product by ID
     */
    public function findProductById(string $id): ?PostpaidProductDetailDto
    {
        foreach ($this->data as $product) {
            if ($product->id === $id) {
                 return $product instanceof PostpaidProductDetailDto ? $product : null;
            }
        }
        return null;
    }

    /**
     * Get available product details
     * 
     * @return PrepaidProductDetailDto[]
     */
    public function getAvailableProducts(): array
    {
        return array_filter($this->data, fn(PostpaidProductDetailDto $product) => $product->isAvailable());
    }
}