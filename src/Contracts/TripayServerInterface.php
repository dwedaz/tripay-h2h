<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Contracts;

use Dwedaz\TripayH2H\DTOs\ServerResponseDto;

interface TripayServerInterface
{
    /**
     * Check Tripay server status
     * 
     * @return ServerResponseDto
     * @throws \Exception when API request fails
     */
    public function checkServer(): ServerResponseDto;
}