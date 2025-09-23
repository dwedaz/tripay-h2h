<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Services;

use Dwedaz\TripayH2H\Contracts\TripayPostpaidInterface;
use Dwedaz\TripayH2H\DTOs\PostpaidCategoriesResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidOperatorsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductsResponseDto;
use Dwedaz\TripayH2H\DTOs\PostpaidProductDetailResponseDto;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\RequestException;
use Exception;

class TripayPostpaidService implements TripayPostpaidInterface
{
    private const SANDBOX_BASE_URL = 'https://tripay.id/api-sandbox/v2';
    private const PRODUCTION_BASE_URL = 'https://tripay.id/api/v2';

    public function __construct(
        private readonly HttpClientFactory $httpClient,
        private readonly string $apiKey,
        private readonly bool $isSandbox = true
    ) {}

    /**
     * Get postpaid bill payment categories
     */
    public function getCategories(?int $categoryId = null): PostpaidCategoriesResponseDto
    {
        try {
            $url = $this->buildUrl('/pembayaran/category', $categoryId);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PostpaidCategoriesResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get postpaid categories: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting postpaid categories: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get postpaid bill payment operators
     */
    public function getOperators(?int $operatorId = null): PostpaidOperatorsResponseDto
    {
        try {
            $url = $this->buildUrl('/pembayaran/operator', $operatorId);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PostpaidOperatorsResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get postpaid operators: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting postpaid operators: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get postpaid bill payment products
     */
    public function getProducts(?int $categoryId = null, ?int $operatorId = null): PostpaidProductsResponseDto
    {
        try {
            $payload = [];
            
            if ($categoryId !== null) {
                $payload['category_id'] = $categoryId;
            }
            
            if ($operatorId !== null) {
                $payload['operator_id'] = $operatorId;
            }
            
            $url = $this->buildUrlWithQuery('/pembayaran/produk', $payload);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PostpaidProductsResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get postpaid products: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting postpaid products: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get postpaid bill payment product detail
     */
    public function getProductDetail(string $code): PostpaidProductDetailResponseDto
    {
        try {
             $payload = ['code' => $code];
            
            $url = $this->buildUrlWithQuery('/pembayaran/produk/cek', $payload);
            
            $response = $this->httpClient
                ->withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($url);

            $response->throw();

            $responseData = $response->json();

            return PostpaidProductDetailResponseDto::fromArray($responseData);

        } catch (RequestException $e) {
            throw new Exception(
                'Failed to get postpaid product detail: ' . $e->getMessage(),
                $e->response?->status() ?? 0,
                $e
            );
        } catch (Exception $e) {
            throw new Exception(
                'Unexpected error while getting postpaid product detail: ' . $e->getMessage(),
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
