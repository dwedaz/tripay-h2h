<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use Dwedaz\TripayH2H\Services\TripayServerService;
use Exception;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class TripayServerServiceTest extends TestCase
{
    private HttpClientFactory $httpClientFactory;
    private PendingRequest $pendingRequest;
    private Response $response;
    private TripayServerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientFactory = Mockery::mock(HttpClientFactory::class);
        $this->pendingRequest = Mockery::mock(PendingRequest::class);
        $this->response = Mockery::mock(Response::class);

        $this->service = new TripayServerService(
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

    public function test_check_server_returns_success_response(): void
    {
        $responseData = [
            'success' => true,
            'message' => 'success cek server, status server online',
            'data' => 1,
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
            ->with('https://tripay.id/api-sandbox/v2/cekserver')
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $this->service->checkServer();

        $this->assertInstanceOf(ServerResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals('success cek server, status server online', $result->message);
        $this->assertEquals(1, $result->data);
    }

    public function test_check_server_returns_error_response(): void
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

        $result = $this->service->checkServer();

        $this->assertInstanceOf(ServerResponseDto::class, $result);
        $this->assertFalse($result->success);
        $this->assertEquals('Internal service error', $result->message);
        $this->assertNull($result->data);
    }

    public function test_check_server_uses_production_url_when_not_sandbox(): void
    {
        $service = new TripayServerService(
            httpClient: $this->httpClientFactory,
            apiKey: 'test-api-key',
            isSandbox: false
        );

        $responseData = [
            'success' => true,
            'message' => 'success',
            'data' => 1,
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
            ->with('https://tripay.id/api/v2/cekserver')
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('throw')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('json')
            ->once()
            ->andReturn($responseData);

        $result = $service->checkServer();

        $this->assertInstanceOf(ServerResponseDto::class, $result);
    }

    public function test_check_server_throws_exception_on_http_error(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unexpected error while checking server status:');

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

        $this->service->checkServer();
    }
}