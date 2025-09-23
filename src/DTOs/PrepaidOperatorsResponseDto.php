<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PrepaidOperatorsResponseDto
{
    /**
     * @param PrepaidOperatorDto[] $data
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
        $operators = [];
        
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $operatorData) {
                $operators[] = PrepaidOperatorDto::fromArray($operatorData);
            }
        }

        return new self(
            success: $response['success'] ?? false,
            message: $response['message'] ?? '',
            data: $operators
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
            'data' => array_map(fn(PrepaidOperatorDto $operator) => $operator->toArray(), $this->data),
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
     * Get total number of operators
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get operators as array
     */
    public function getOperators(): array
    {
        return $this->data;
    }

    /**
     * Get only available operators
     */
    public function getAvailableOperators(): array
    {
        return array_filter($this->data, fn(PrepaidOperatorDto $operator) => $operator->isAvailable());
    }

    /**
     * Find operator by ID
     */
    public function findOperatorById(string $id): ?PrepaidOperatorDto
    {
        foreach ($this->data as $operator) {
            if ($operator->id === $id) {
                return $operator;
            }
        }
        return null;
    }
}