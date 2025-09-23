<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Services;

use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\RequestException;
use Exception;

class TripayServerService implements TripayServerInterface
{
    private const SANDBOX_BASE_URL = 'https://tripay.id/api-sandbox/v2';
    private const PRODUCTION_BASE_URL = 'https://tripay.id/api/v2';

    public function __construct(
        private readonly HttpClientFactory $httpClient,
        private readonly string $apiKey,
        private readonly bool $isSandbox = true
    ) {}

    /**
     * Check Tripay server status
     */
    public function checkServer(): ServerResponseDto
    {
        try {
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($this->buildUrl('/cekserver'));

            $response->throw();

            $responseData = $response->json();

            return ServerResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to check server status: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while checking server status: ' . $e->getMessage(),
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