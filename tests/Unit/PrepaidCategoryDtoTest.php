<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\PrepaidCategoryDto;
use PHPUnit\Framework\TestCase;

class PrepaidCategoryDtoTest extends TestCase
{
    public function test_can_create_dto_from_array(): void
    {
        $data = [
            'id' => '1',
            'product_name' => 'Pulsa All Operator',
            'type' => 'PULSA',
            'status' => '1',
        ];

        $dto = PrepaidCategoryDto::fromArray($data);

        $this->assertEquals('1', $dto->id);
        $this->assertEquals('Pulsa All Operator', $dto->name);
        $this->assertEquals('PULSA', $dto->type);
        $this->assertEquals('1', $dto->status);
    }

    public function test_can_create_dto_with_missing_fields(): void
    {
        $data = [
            'id' => '2',
        ];

        $dto = PrepaidCategoryDto::fromArray($data);

        $this->assertEquals('2', $dto->id);
        $this->assertEquals('', $dto->name);
        $this->assertEquals('', $dto->type);
        $this->assertEquals('0', $dto->status);
    }

    public function test_can_convert_dto_to_array(): void
    {
        $dto = new PrepaidCategoryDto(
            id: '1',
            name: 'Token Listrik',
            type: 'PLN',
            status: '1'
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'id' => '1',
            'product_name' => 'Token Listrik',
            'type' => 'PLN',
            'status' => '1',
        ], $array);
    }

    public function test_is_available_returns_true_when_status_is_1(): void
    {
        $dto = new PrepaidCategoryDto('1', 'Test', 'PULSA', '1');

        $this->assertTrue($dto->isAvailable());
        $this->assertFalse($dto->isUnavailable());
        $this->assertTrue($dto->getStatusBoolean());
    }

    public function test_is_available_returns_false_when_status_is_0(): void
    {
        $dto = new PrepaidCategoryDto('1', 'Test', 'PULSA', '0');

        $this->assertFalse($dto->isAvailable());
        $this->assertTrue($dto->isUnavailable());
        $this->assertFalse($dto->getStatusBoolean());
    }

    public function test_get_status_text_returns_correct_text(): void
    {
        $availableDto = new PrepaidCategoryDto('1', 'Test', 'PULSA', '1');
        $unavailableDto = new PrepaidCategoryDto('1', 'Test', 'PULSA', '0');

        $this->assertEquals('Available', $availableDto->getStatusText());
        $this->assertEquals('Unavailable', $unavailableDto->getStatusText());
    }

    public function test_is_type_works_case_insensitive(): void
    {
        $dto = new PrepaidCategoryDto('1', 'Test', 'PULSA', '1');

        $this->assertTrue($dto->isType('PULSA'));
        $this->assertTrue($dto->isType('pulsa'));
        $this->assertTrue($dto->isType('Pulsa'));
        $this->assertFalse($dto->isType('GAME'));
    }

    public function test_type_specific_methods(): void
    {
        $pulsaDto = new PrepaidCategoryDto('1', 'Pulsa', 'PULSA', '1');
        $gameDto = new PrepaidCategoryDto('2', 'Game', 'GAME', '1');
        $plnDto = new PrepaidCategoryDto('3', 'PLN', 'PLN', '1');

        // Test pulsa
        $this->assertTrue($pulsaDto->isPulsa());
        $this->assertFalse($pulsaDto->isGame());
        $this->assertFalse($pulsaDto->isPln());

        // Test game
        $this->assertFalse($gameDto->isPulsa());
        $this->assertTrue($gameDto->isGame());
        $this->assertFalse($gameDto->isPln());

        // Test PLN
        $this->assertFalse($plnDto->isPulsa());
        $this->assertFalse($plnDto->isGame());
        $this->assertTrue($plnDto->isPln());
    }

    public function test_handles_empty_array(): void
    {
        $dto = PrepaidCategoryDto::fromArray([]);

        $this->assertEquals('', $dto->id);
        $this->assertEquals('', $dto->name);
        $this->assertEquals('', $dto->type);
        $this->assertEquals('0', $dto->status);
        $this->assertFalse($dto->isAvailable());
    }

    public function test_type_casting_works_correctly(): void
    {
        $data = [
            'id' => 123, // integer
            'product_name' => 456, // integer
            'type' => 789, // integer
            'status' => 1, // integer
        ];

        $dto = PrepaidCategoryDto::fromArray($data);

        $this->assertIsString($dto->id);
        $this->assertIsString($dto->name);
        $this->assertIsString($dto->type);
        $this->assertIsString($dto->status);
        
        $this->assertEquals('123', $dto->id);
        $this->assertEquals('456', $dto->name);
        $this->assertEquals('789', $dto->type);
        $this->assertEquals('1', $dto->status);
    }
}