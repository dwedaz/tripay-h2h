<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoryDto;
use PHPUnit\Framework\TestCase;

class PrepaidCategoriesResponseDtoTest extends TestCase
{
    public function test_can_create_response_dto_from_success_array(): void
    {
        $responseData = [
            'success' => true,
            'data' => [
                [
                    'id' => '1',
                    'product_name' => 'Pulsa All Operator',
                    'type' => 'PULSA',
                    'status' => '1',
                ],
                [
                    'id' => '2',
                    'product_name' => 'Voucher Game',
                    'type' => 'GAME',
                    'status' => '0',
                ],
            ],
        ];

        $dto = PrepaidCategoriesResponseDto::fromArray($responseData);

        $this->assertTrue($dto->success);
        $this->assertEquals('', $dto->message);
        $this->assertCount(2, $dto->data);
        $this->assertInstanceOf(PrepaidCategoryDto::class, $dto->data[0]);
        $this->assertEquals('1', $dto->data[0]->id);
        $this->assertEquals('Pulsa All Operator', $dto->data[0]->name);
    }

    public function test_can_create_response_dto_from_error_array(): void
    {
        $responseData = [
            'success' => false,
            'message' => 'Invalid API Key',
        ];

        $dto = PrepaidCategoriesResponseDto::fromArray($responseData);

        $this->assertFalse($dto->success);
        $this->assertEquals('Invalid API Key', $dto->message);
        $this->assertEmpty($dto->data);
    }

    public function test_can_convert_response_dto_to_array(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Pulsa', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Game', 'GAME', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(
            success: true,
            message: 'Success',
            data: $categories
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'success' => true,
            'message' => 'Success',
            'data' => [
                [
                    'id' => '1',
                    'product_name' => 'Pulsa',
                    'type' => 'PULSA',
                    'status' => '1',
                ],
                [
                    'id' => '2',
                    'product_name' => 'Game',
                    'type' => 'GAME',
                    'status' => '1',
                ],
            ],
        ], $array);
    }

    public function test_has_error_returns_correct_values(): void
    {
        $successDto = new PrepaidCategoriesResponseDto(true, '', []);
        $errorDto = new PrepaidCategoriesResponseDto(false, 'Error', []);

        $this->assertFalse($successDto->hasError());
        $this->assertTrue($errorDto->hasError());
    }

    public function test_has_data_returns_correct_values(): void
    {
        $categories = [new PrepaidCategoryDto('1', 'Test', 'PULSA', '1')];
        
        $dtoWithData = new PrepaidCategoriesResponseDto(true, '', $categories);
        $dtoWithoutData = new PrepaidCategoriesResponseDto(true, '', []);
        $errorDto = new PrepaidCategoriesResponseDto(false, 'Error', $categories);

        $this->assertTrue($dtoWithData->hasData());
        $this->assertFalse($dtoWithoutData->hasData());
        $this->assertFalse($errorDto->hasData());
    }

    public function test_count_returns_correct_number(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Test1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Test2', 'GAME', '1'),
            new PrepaidCategoryDto('3', 'Test3', 'PLN', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);

        $this->assertEquals(3, $dto->count());
    }

    public function test_get_categories_returns_all_categories(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Test1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Test2', 'GAME', '0'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);

        $this->assertSame($categories, $dto->getCategories());
    }

    public function test_get_available_categories_filters_correctly(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Available', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Unavailable', 'GAME', '0'),
            new PrepaidCategoryDto('3', 'Available2', 'PLN', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);
        $availableCategories = $dto->getAvailableCategories();

        $this->assertCount(2, $availableCategories);
        $this->assertEquals('1', $availableCategories[0]->id);
        $this->assertEquals('3', $availableCategories[2]->id);
    }

    public function test_get_categories_by_type_filters_correctly(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Pulsa1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Game1', 'GAME', '1'),
            new PrepaidCategoryDto('3', 'Pulsa2', 'PULSA', '0'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);
        $pulsaCategories = $dto->getCategoriesByType('PULSA');

        $this->assertCount(2, $pulsaCategories);
        $this->assertEquals('1', $pulsaCategories[0]->id);
        $this->assertEquals('3', $pulsaCategories[2]->id);
    }

    public function test_get_available_categories_by_type_filters_correctly(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Available Pulsa', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Unavailable Pulsa', 'PULSA', '0'),
            new PrepaidCategoryDto('3', 'Available Game', 'GAME', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);
        $availablePulsa = $dto->getAvailableCategoriesByType('PULSA');

        $this->assertCount(1, $availablePulsa);
        $this->assertEquals('1', $availablePulsa[0]->id);
        $this->assertEquals('Available Pulsa', $availablePulsa[0]->name);
    }

    public function test_find_category_by_id_returns_correct_category(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Test1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Test2', 'GAME', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);

        $foundCategory = $dto->findCategoryById('2');
        $notFoundCategory = $dto->findCategoryById('99');

        $this->assertNotNull($foundCategory);
        $this->assertEquals('2', $foundCategory->id);
        $this->assertEquals('Test2', $foundCategory->name);
        $this->assertNull($notFoundCategory);
    }

    public function test_get_available_types_returns_unique_types(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Pulsa1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Pulsa2', 'PULSA', '1'),
            new PrepaidCategoryDto('3', 'Game1', 'GAME', '1'),
            new PrepaidCategoryDto('4', 'UnavailableGame', 'GAME', '0'),
            new PrepaidCategoryDto('5', 'PLN1', 'PLN', '1'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);
        $availableTypes = $dto->getAvailableTypes();

        $this->assertCount(3, $availableTypes);
        $this->assertContains('PULSA', $availableTypes);
        $this->assertContains('GAME', $availableTypes);
        $this->assertContains('PLN', $availableTypes);
    }

    public function test_type_specific_methods_return_correct_categories(): void
    {
        $categories = [
            new PrepaidCategoryDto('1', 'Pulsa1', 'PULSA', '1'),
            new PrepaidCategoryDto('2', 'Game1', 'GAME', '1'),
            new PrepaidCategoryDto('3', 'PLN1', 'PLN', '1'),
            new PrepaidCategoryDto('4', 'Pulsa2', 'PULSA', '0'),
        ];

        $dto = new PrepaidCategoriesResponseDto(true, '', $categories);

        $pulsaCategories = $dto->getPulsaCategories();
        $gameCategories = $dto->getGameCategories();
        $plnCategories = $dto->getPlnCategories();

        $this->assertCount(2, $pulsaCategories);
        $this->assertCount(1, $gameCategories);
        $this->assertCount(1, $plnCategories);

        $this->assertEquals('PULSA', $pulsaCategories[0]->type);
        $this->assertEquals('GAME', $gameCategories[1]->type);
        $this->assertEquals('PLN', $plnCategories[2]->type);
    }

    public function test_handles_missing_data_gracefully(): void
    {
        $responseData = [
            'success' => true,
        ];

        $dto = PrepaidCategoriesResponseDto::fromArray($responseData);

        $this->assertTrue($dto->success);
        $this->assertEquals('', $dto->message);
        $this->assertEmpty($dto->data);
        $this->assertEquals(0, $dto->count());
        $this->assertFalse($dto->hasData());
    }

    public function test_handles_empty_array_gracefully(): void
    {
        $dto = PrepaidCategoriesResponseDto::fromArray([]);

        $this->assertFalse($dto->success);
        $this->assertEquals('', $dto->message);
        $this->assertEmpty($dto->data);
    }
}