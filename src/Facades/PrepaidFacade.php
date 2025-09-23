<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Facades;

use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDetailResponseDto;

class PrepaidFacade
{
    private TripayPrepaidInterface $prepaidService;

    public function __construct(TripayPrepaidInterface $prepaidService)
    {
        $this->prepaidService = $prepaidService;
    }

    /**
     * Get prepaid product categories
     */
    public function getCategories(?int $categoryId = null): PrepaidCategoriesResponseDto
    {
        return $this->prepaidService->getCategories($categoryId);
    }

    /**
     * Get prepaid operators
     */
    public function getOperators(?int $operatorId = null, ?string $categoryId = null): PrepaidOperatorsResponseDto
    {
        return $this->prepaidService->getOperators($operatorId, $categoryId);
    }

    /**
     * Get prepaid products
     */
    public function getProducts(?int $categoryId = null, ?string $operatorId = null): PrepaidProductsResponseDto
    {
        return $this->prepaidService->getProducts($categoryId, $operatorId);
    }
    
    /**
     * Get prepaid product detail by code
     */
    public function getProductDetail(string $code): PrepaidProductDetailResponseDto
    {
        return $this->prepaidService->getProductDetail($code);
    }
}