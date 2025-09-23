<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\DTOs;

readonly class PostpaidOperatorsResponseDto
{
    /**
     * @param bool $success
     * @param string $message
     * @param PostpaidOperatorDto[] $data
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
                $data[] = PostpaidOperatorDto::fromArray($item);
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
            'data' => array_map(fn(PostpaidOperatorDto $operator) => $operator->toArray(), $this->data),
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
     * Get count of operators
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get all operators
     * 
     * @return PostpaidOperatorDto[]
     */
    public function getOperators(): array
    {
        return $this->data;
    }

    /**
     * Get available operators
     * 
     * @return PostpaidOperatorDto[]
     */
    public function getAvailableOperators(): array
    {
        return array_filter($this->data, fn(PostpaidOperatorDto $operator) => $operator->isAvailable());
    }

    /**
     * Find operator by ID
     */
    public function findOperatorById(string $id): ?PostpaidOperatorDto
    {
        foreach ($this->data as $operator) {
            if ($operator->id === $id) {
                return $operator;
            }
        }
        return null;
    }

    /**
     * Find operator by name
     */
    public function findOperatorByName(string $name): ?PostpaidOperatorDto
    {
        foreach ($this->data as $operator) {
            if (stripos($operator->name, $name) !== false) {
                return $operator;
            }
        }
        return null;
    }

    /**
     * Get first operator
     */
    public function getFirstOperator(): ?PostpaidOperatorDto
    {
        return $this->data[0] ?? null;
    }

    /**
     * Get operators by availability status
     * 
     * @param bool $available
     * @return PostpaidOperatorDto[]
     */
    public function getOperatorsByAvailability(bool $available): array
    {
        return array_filter($this->data, function(PostpaidOperatorDto $operator) use ($available) {
            return $operator->isAvailable() === $available;
        });
    }

    /**
     * Check if any operators are available
     */
    public function hasAvailableOperators(): bool
    {
        foreach ($this->data as $operator) {
            if ($operator->isAvailable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get count of available operators
     */
    public function countAvailableOperators(): int
    {
        return count($this->getAvailableOperators());
    }
}
