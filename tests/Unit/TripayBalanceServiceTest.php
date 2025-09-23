<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use Dwedaz\TripayH2H\Services\TripayBalanceService;
use Exception;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class TripayBalanceServiceTest extends TestCase
{
    private HttpClientFactory $httpClientFactory;
    private PendingRequest $pendingRequest;
    private Response $response;
    private TripayBalanceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientFactory = Mockery::mock(HttpClientFactory::class);
        $this->pendingRequest = Mockery::mock(PendingRequest::class);
        $this->response = Mockery::mock(Response::class);

        $this->service = new TripayBalanceService(
            httpClient: $this->httpClientFactory,
            apiKey: 'test-api-key',
            isSandbox: true
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_check_balance_returns_success_response(): void
    {
        $responseData = [
            'success' => true,
            'message' => 'Saldo anda Rp. 1.000.000.000',
            'data' => 1000000000,
        ];

        $this->httpClientFactory
            ->shouldReceive('withHeaders')
            ->once()
            ->with([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer test-api-key',
            ])
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('timeout')
            ->once()
            ->with(30)
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('get')
            ->once()
            ->with('https://tripay.id/api-sandbox/v2/ceksaldo')
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $this->service->checkBalance();

        $this->assertInstanceOf(BalanceResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals('Saldo anda Rp. 1.000.000.000', $result->message);
        $this->assertEquals(1000000000, $result->data);
    }

    public function test_check_balance_returns_error_response(): void
    {
        $responseData = [
            'success' => false,
            'message' => 'Internal service error',
        ];

        $this->httpClientFactory
            ->shouldReceive('withHeaders')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('timeout')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('get')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $this->service->checkBalance();

        $this->assertInstanceOf(BalanceResponseDto::class, $result);
        $this->assertFalse($result->success);
        $this->assertEquals('Internal service error', $result->message);
        $this->assertNull($result->data);
    }

    public function test_check_balance_uses_production_url_when_not_sandbox(): void
    {
        $service = new TripayBalanceService(
            httpClient: $this->httpClientFactory,
            apiKey: 'test-api-key',
            isSandbox: false
        );

        $responseData = [
            'success' => true,
            'message' => 'Balance success',
            'data' => 5000000,
        ];

        $this->httpClientFactory
            ->shouldReceive('withHeaders')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('timeout')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('get')
            ->once()
            ->with('https://tripay.id/api/v2/ceksaldo')
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $service->checkBalance();

        $this->assertInstanceOf(BalanceResponseDto::class, $result);
    }

    public function test_check_balance_throws_exception_on_http_error(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unexpected error while checking balance:');

        $this->httpClientFactory
            ->shouldReceive('withHeaders')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('timeout')
            ->once()
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('get')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andThrow(new Exception('HTTP Error'));

        $this->service->checkBalance();
    }

    public function test_check_balance_sends_correct_headers(): void
    {
        $responseData = [
            'success' => true,
            'message' => 'Balance available',
            'data' => 2000000,
        ];

        $this->httpClientFactory
            ->shouldReceive('withHeaders')
            ->once()
            ->with([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer test-api-key',
            ])
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('timeout')
            ->once()
            ->with(30)
            ->andReturn($this->pendingRequest);

        $this->pendingRequest
            ->shouldReceive('get')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $this->service->checkBalance();

        $this->assertInstanceOf(BalanceResponseDto::class, $result);
        $this->assertEquals(2000000, $result->data);
    }
}