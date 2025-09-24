<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Services;

use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\Contracts\TripayPostpaidInterface;
use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDetailResponseDto;
use Dwedaz\TripayH2H\Facades\PrepaidFacade;
use Dwedaz\TripayH2H\Facades\PostpaidFacade;

class TripayService
{
    public function __construct(
        private readonly TripayServerInterface $serverService,
        private readonly TripayBalanceInterface $balanceService,
        private readonly TripayPrepaidInterface $prepaidService,
        private readonly TripayPostpaidInterface $postpaidService
    ) {}

    /**
     * Check Tripay server status
     */
    public function checkServer(): ServerResponseDto
    {
        return $this->serverService->checkServer();
    }

    /**
     * Check Tripay account balance
     */
    public function checkBalance(): BalanceResponseDto
    {
        return $this->balanceService->checkBalance();
    }
    
    /**
     * Get prepaid product categories
     */
    public function getCategories(?string $categoryId = null): PrepaidCategoriesResponseDto
    {
        return $this->prepaidService->getCategories($categoryId);
    }
    
    /**
     * Get prepaid operators
     */
    public function getOperators(?string $operatorId = null, ?string $categoryId = null): PrepaidOperatorsResponseDto
    {
        return $this->prepaidService->getOperators($operatorId, $categoryId);
    }
    
    /**
     * Get prepaid products
     */
    public function getProducts(?string $categoryId = null, ?string $operatorId = null): PrepaidProductsResponseDto
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

    /**
     * Get server service instance
     */
    public function server(): TripayServerInterface
    {
        return $this->serverService;
    }

    /**
     * Get balance service instance
     */
    public function balance(): TripayBalanceInterface
    {
        return $this->balanceService;
    }

    /**
     * Get prepaid facade instance
     */
    public function prepaid(): PrepaidFacade
    {
        return new PrepaidFacade($this->prepaidService);
    }

    /**
     * Get postpaid facade instance
     */
    public function postpaid(): PostpaidFacade
    {
        return new PostpaidFacade($this->postpaidService);
    }

}
