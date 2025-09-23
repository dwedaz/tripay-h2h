<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use PHPUnit\Framework\TestCase;

class ServerResponseDtoTest extends TestCase
{
    public function test_can_create_dto_from_success_response(): void
    {
        $responseData = [
            'success' => true,
            'message' => 'success cek server, status server online',
            'data' => 1,
        ];

        $dto = ServerResponseDto::fromArray($responseData);

        $this->assertTrue($dto->success);
        $this->assertEquals('success cek server, status server online', $dto->message);
        $this->assertEquals(1, $dto->data);
    }

    public function test_can_create_dto_from_error_response(): void
    {
        $responseData = [
            'success' => false,
            'message' => 'Internal service error',
        ];

        $dto = ServerResponseDto::fromArray($responseData);

        $this->assertFalse($dto->success);
        $this->assertEquals('Internal service error', $dto->message);
        $this->assertNull($dto->data);
    }

    public function test_can_convert_dto_to_array(): void
    {
        $dto = new ServerResponseDto(
            success: true,
            message: 'Test message',
            data: 1
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'success' => true,
            'message' => 'Test message',
            'data' => 1,
        ], $array);
    }

    public function test_is_server_online_returns_true_when_success_and_data_is_1(): void
    {
        $dto = new ServerResponseDto(
            success: true,
            message: 'Server online',
            data: 1
        );

        $this->assertTrue($dto->isServerOnline());
    }

    public function test_is_server_online_returns_false_when_success_is_false(): void
    {
        $dto = new ServerResponseDto(
            success: false,
            message: 'Server error',
            data: null
        );

        $this->assertFalse($dto->isServerOnline());
    }

    public function test_is_server_online_returns_false_when_data_is_not_1(): void
    {
        $dto = new ServerResponseDto(
            success: true,
            message: 'Response ok',
            data: 0
        );

        $this->assertFalse($dto->isServerOnline());
    }

    public function test_has_error_returns_true_when_success_is_false(): void
    {
        $dto = new ServerResponseDto(
            success: false,
            message: 'Error occurred',
            data: null
        );

        $this->assertTrue($dto->hasError());
    }

    public function test_has_error_returns_false_when_success_is_true(): void
    {
        $dto = new ServerResponseDto(
            success: true,
            message: 'All good',
            data: 1
        );

        $this->assertFalse($dto->hasError());
    }

    public function test_handles_missing_array_keys_gracefully(): void
    {
        $responseData = []; // Empty array

        $dto = ServerResponseDto::fromArray($responseData);

        $this->assertFalse($dto->success);
        $this->assertEquals('', $dto->message);
        $this->assertNull($dto->data);
    }
}