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
use Dwedaz\TripayH2H\Facades\PrepaidFacade;
use Dwedaz\TripayH2H\Services\TripayService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ServerResponseDto checkServer()
 * @method static BalanceResponseDto checkBalance()
 * @method static TripayServerInterface server()
 * @method static TripayBalanceInterface balance()
 * @method static PrepaidFacade prepaid()
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