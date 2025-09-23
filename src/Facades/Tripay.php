<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Facades;

use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDetailResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductsResponseDto;
use Dwedaz\TripayH2H\Services\TripayService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ServerResponseDto checkServer()
 * @method static BalanceResponseDto checkBalance()
 * @method static PrepaidCategoriesResponseDto getCategories(?string $categoryId = null)
 * @method static PrepaidOperatorsResponseDto getOperators(?string $operatorId = null, ?string $categoryId = null)
 * @method static PrepaidProductsResponseDto getProducts(?string $categoryId = null, ?string $operatorId = null)
 * @method static PrepaidProductDetailResponseDto getProductDetail(string $code)
 * @method static PostpaidCategoriesResponseDto getPostpaidCategories(?string $categoryId = null)
 * @method static PostpaidOperatorsResponseDto getPostpaidOperators(?string $operatorId = null)
 * @method static PostpaidProductsResponseDto getPostpaidProducts(?string $categoryId = null, ?string $operatorId = null)
 * @method static TripayServerInterface server()
 * @method static TripayBalanceInterface balance()
 * @method static PrepaidFacade prepaid()
 * @method static PostpaidFacade postpaid()
 * 
 * @see TripayService
 */
class Tripay extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tripay';
    }
}