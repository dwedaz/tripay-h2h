<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Contracts;

use Dwedaz\TripayH2H\DTOs\PostpaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductsResponseDto;
use Dwedaz\TripayH2h\DTOs\PostpaidProductDetailResponseDto;

interface TripayPostpaidInterface
{
    /**
     * Get postpaid bill payment categories
     * 
     * @param string|null $categoryId Optional category ID filter
     * @return PostpaidCategoriesResponseDto
     * @throws \Exception when API request fails
     */
    public function getCategories(?int $categoryId = null): PostpaidCategoriesResponseDto;
    
    /**
     * Get postpaid bill payment operators
     * 
     * @param string|null $operatorId Optional operator ID filter
     * @return PostpaidOperatorsResponseDto
     * @throws \Exception when API request fails
     */
    public function getOperators(?int $operatorId = null): PostpaidOperatorsResponseDto;
    
    /**
     * Get postpaid bill payment products
     * 
     * @param string|null $categoryId Optional category ID filter
     * @param string|null $operatorId Optional operator ID filter
     * @return PostpaidProductsResponseDto
     * @throws \Exception when API request fails
     */
    public function getProducts(?int $categoryId = null, ?int $operatorId = null): PostpaidProductsResponseDto;

    /**
     * Get prepaid product detail by code
     * 
     * @param string $code Product code to check
     * @return PrepaidProductDetailResponseDto
     * @throws \Exception when API request fails
     */
    public function getProductDetail(string $code): PostpaidProductDetailResponseDto;
}

