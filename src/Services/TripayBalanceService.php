<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Services;

use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\RequestException;
use Exception;

class TripayBalanceService implements TripayBalanceInterface
{
    private const SANDBOX_BASE_URL = 'https://tripay.id/api-sandbox/v2';
    private const PRODUCTION_BASE_URL = 'https://tripay.id/api/v2';

    public function __construct(
        private readonly HttpClientFactory $httpClient,
        private readonly string $apiKey,
        private readonly bool $isSandbox = true
    ) {}

    /**
     * Check Tripay account balance
     */
    public function checkBalance(): BalanceResponseDto
    {
        try {
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($this->buildUrl('/ceksaldo'));

            $response->throw();

            $responseData = $response->json();

            return BalanceResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to check balance: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while checking balance: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get HTTP headers for API request
     */
    private function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }

    /**
     * Build complete URL for API endpoint
     */
    private function buildUrl(string $endpoint): string
    {
        $baseUrl = $this->isSandbox ? self::SANDBOX_BASE_URL : self::PRODUCTION_BASE_URL;
        
        return $baseUrl . $endpoint;
    }
}