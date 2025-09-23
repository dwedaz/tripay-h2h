<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use PHPUnit\Framework\TestCase;

class BalanceResponseDtoTest extends TestCase
{
    public function test_can_create_dto_from_success_response(): void
    {
        $responseData = [
            'success' => true,
            'message' => 'Saldo anda Rp. 1.000.000.000',
            'data' => 1000000000,
        ];

        $dto = BalanceResponseDto::fromArray($responseData);

        $this->assertTrue($dto->success);
        $this->assertEquals('Saldo anda Rp. 1.000.000.000', $dto->message);
        $this->assertEquals(1000000000, $dto->data);
    }

    public function test_can_create_dto_from_error_response(): void
    {
        $responseData = [
            'success' => false,
            'message' => 'Internal service error',
        ];

        $dto = BalanceResponseDto::fromArray($responseData);

        $this->assertFalse($dto->success);
        $this->assertEquals('Internal service error', $dto->message);
        $this->assertNull($dto->data);
    }

    public function test_can_convert_dto_to_array(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Test balance message',
            data: 5000000
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'success' => true,
            'message' => 'Test balance message',
            'data' => 5000000,
        ], $array);
    }

    public function test_get_balance_returns_data_value(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance available',
            data: 2500000
        );

        $this->assertEquals(2500000, $dto->getBalance());
    }

    public function test_get_balance_returns_null_when_no_data(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error',
            data: null
        );

        $this->assertNull($dto->getBalance());
    }

    public function test_get_formatted_balance_formats_currency_correctly(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 1250000
        );

        $this->assertEquals('Rp. 1.250.000', $dto->getFormattedBalance());
    }

    public function test_get_formatted_balance_returns_zero_when_null(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error',
            data: null
        );

        $this->assertEquals('Rp. 0', $dto->getFormattedBalance());
    }

    public function test_is_sufficient_for_returns_true_when_balance_is_enough(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 1000000
        );

        $this->assertTrue($dto->isSufficientFor(500000));
        $this->assertTrue($dto->isSufficientFor(1000000));
    }

    public function test_is_sufficient_for_returns_false_when_balance_is_not_enough(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 100000
        );

        $this->assertFalse($dto->isSufficientFor(500000));
    }

    public function test_is_sufficient_for_returns_false_when_no_success(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error',
            data: 1000000
        );

        $this->assertFalse($dto->isSufficientFor(500000));
    }

    public function test_is_sufficient_for_returns_false_when_data_is_null(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: null
        );

        $this->assertFalse($dto->isSufficientFor(500000));
    }

    public function test_has_balance_returns_true_when_success_and_has_data(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 1000000
        );

        $this->assertTrue($dto->hasBalance());
    }

    public function test_has_balance_returns_false_when_no_success(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error',
            data: 1000000
        );

        $this->assertFalse($dto->hasBalance());
    }

    public function test_has_balance_returns_false_when_data_is_null(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: null
        );

        $this->assertFalse($dto->hasBalance());
    }

    public function test_has_error_returns_true_when_success_is_false(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error occurred',
            data: null
        );

        $this->assertTrue($dto->hasError());
    }

    public function test_has_error_returns_false_when_success_is_true(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'All good',
            data: 1000000
        );

        $this->assertFalse($dto->hasError());
    }

    public function test_is_low_balance_returns_true_when_below_threshold(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 50000
        );

        $this->assertTrue($dto->isLowBalance(100000));
        $this->assertTrue($dto->isLowBalance()); // default threshold 100000
    }

    public function test_is_low_balance_returns_false_when_above_threshold(): void
    {
        $dto = new BalanceResponseDto(
            success: true,
            message: 'Balance',
            data: 150000
        );

        $this->assertFalse($dto->isLowBalance(100000));
        $this->assertFalse($dto->isLowBalance()); // default threshold 100000
    }

    public function test_is_low_balance_returns_false_when_no_balance(): void
    {
        $dto = new BalanceResponseDto(
            success: false,
            message: 'Error',
            data: null
        );

        $this->assertFalse($dto->isLowBalance(100000));
    }

    public function test_handles_missing_array_keys_gracefully(): void
    {
        $responseData = []; // Empty array

        $dto = BalanceResponseDto::fromArray($responseData);

        $this->assertFalse($dto->success);
        $this->assertEquals('', $dto->message);
        $this->assertNull($dto->data);
    }
}