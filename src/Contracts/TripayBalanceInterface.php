<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Contracts;

use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;

interface TripayBalanceInterface
{
    /**
     * Check Tripay account balance
     * 
     * @return BalanceResponseDto
     * @throws \Exception when API request fails
     */
    public function checkBalance(): BalanceResponseDto;
}