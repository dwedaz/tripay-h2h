<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Contracts;

use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDetailResponseDto;

interface TripayPrepaidInterface
{
    /**
     * Get prepaid product categories
     * 
     * @param string|null $categoryId Optional category ID filter
     * @return PrepaidCategoriesResponseDto
     * @throws \Exception when API request fails
     */
    public function getCategories(?int $categoryId = null): PrepaidCategoriesResponseDto;
    
    /**
     * Get prepaid operators
     * 
     * @param string|null $operatorId Optional operator ID filter
     * @param string|null $categoryId Optional category ID filter
     * @return PrepaidOperatorsResponseDto
     * @throws \Exception when API request fails
     */
    public function getOperators(?int $operatorId = null, ?int $categoryId = null): PrepaidOperatorsResponseDto;
    
    /**
     * Get prepaid products
     * 
     * @param string|null $categoryId Optional category ID filter
     * @param string|null $operatorId Optional operator ID filter
     * @return PrepaidProductsResponseDto
     * @throws \Exception when API request fails
     */
    public function getProducts(?int $categoryId = null, ?int $operatorId = null): PrepaidProductsResponseDto;
    
    /**
     * Get prepaid product detail by code
     * 
     * @param string $code Product code to check
     * @return PrepaidProductDetailResponseDto
     * @throws \Exception when API request fails
     */
    public function getProductDetail(string $code): PrepaidProductDetailResponseDto;
}