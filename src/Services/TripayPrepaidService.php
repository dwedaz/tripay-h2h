<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Services;

use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\DTOs\PrepaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PrepaidProductDetailResponseDto;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\RequestException;
use Exception;

class TripayPrepaidService implements TripayPrepaidInterface
{
    private const SANDBOX_BASE_URL = 'https://tripay.id/api-sandbox/v2';
    private const PRODUCTION_BASE_URL = 'https://tripay.id/api/v2';

    public function __construct(
        private readonly HttpClientFactory $httpClient,
        private readonly string $apiKey,
        private readonly bool $isSandbox = true
    ) {}

    /**
     * Get prepaid product categories
     */
    public function getCategories(?int $categoryId = null): PrepaidCategoriesResponseDto
    {
        try {
            $url = $this->buildUrl('/pembelian/category', $categoryId);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PrepaidCategoriesResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get prepaid categories: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting prepaid categories: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get prepaid operators
     */
    public function getOperators(?int $operatorId = null, ?int $categoryId = null): PrepaidOperatorsResponseDto
    {
        try {
            $payload = [];
            
            if ($operatorId !== null) {
                $payload['operator_id'] = $operatorId;
            }
            
            if ($categoryId !== null) {
                $payload['category_id'] = $categoryId;
            }
            
            $url = $this->buildUrlWithQuery('/pembelian/operator', $payload);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PrepaidOperatorsResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get prepaid operators: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting prepaid operators: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
    
    /**
     * Get prepaid products
     */
    public function getProducts(?int $categoryId = null, ?int $operatorId = null): PrepaidProductsResponseDto
    {
        try {
            $payload = [];
            
            if ($categoryId !== null) {
                $payload['category_id'] = $categoryId;
            }
            
            if ($operatorId !== null) {
                $payload['operator_id'] = $operatorId;
            }
            
            $url = $this->buildUrlWithQuery('/pembelian/produk', $payload);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PrepaidProductsResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get prepaid products: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting prepaid products: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
    
    /**
     * Get prepaid product detail by code
     */
    public function getProductDetail(string $code): PrepaidProductDetailResponseDto
    {
        try {
            $payload = ['code' => $code];
            
            $url = $this->buildUrlWithQuery('/pembelian/produk/cek', $payload);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();
            

            return PrepaidProductDetailResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get prepaid product detail: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting prepaid product detail: ' . $e->getMessage(),
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
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];
    }

    /**
     * Build API URL with path and optional ID
     */
    private function buildUrl(string $path, ?string $id = null): string
    {
        $baseUrl = $this->isSandbox ? self::SANDBOX_BASE_URL : self::PRODUCTION_BASE_URL;
        
        if ($id !== null) {
            $path .= '/' . $id;
        }
        
        return $baseUrl . $path;
    }

    /**
     * Build API URL with query parameters
     */
    private function buildUrlWithQuery(string $path, array $params = []): string
    {
        $baseUrl = $this->isSandbox ? self::SANDBOX_BASE_URL : self::PRODUCTION_BASE_URL;
        $url = $baseUrl . $path;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
}