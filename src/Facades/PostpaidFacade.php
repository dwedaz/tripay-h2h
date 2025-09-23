<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Facades;

use Dwedaz\TripayH2H\Contracts\TripayPostpaidInterface;
use Dwedaz\TripayH2H\DTOs\PostpaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductDetailResponseDto;

class PostpaidFacade
{
    private TripayPostpaidInterface $postpaidService;

    public function __construct(TripayPostpaidInterface $postpaidService)
    {
        $this->postpaidService = $postpaidService;
    }

    /**
     * Get postpaid bill payment categories
     */
    public function getCategories(?string $categoryId = null): PostpaidCategoriesResponseDto
    {
        return $this->postpaidService->getCategories($categoryId);
    }

    /**
     * Get postpaid bill payment operators
     */
    public function getOperators(?string $operatorId = null): PostpaidOperatorsResponseDto
    {
        return $this->postpaidService->getOperators($operatorId);
    }

    /**
     * Get postpaid bill payment products
     */
    public function getProducts(?string $categoryId = null, ?string $operatorId = null): PostpaidProductsResponseDto
    {
        return $this->postpaidService->getProducts($categoryId, $operatorId);
    }

  /**
     * Get prepaid product detail by code
     */
    public function getProductDetail(string $code): PostpaidProductDetailResponseDto
    {
        return $this->postpaidService->getProductDetail($code);
    }
}

