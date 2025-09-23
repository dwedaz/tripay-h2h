<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Tests\Unit;

use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidCategoryInterface;
use Dwedaz\TripayH2H\DTOs\BalanceResponseDto;
use Dwedaz\TripayH2H\DTOs\ServerResponseDto;
use Dwedaz\TripayH2H\Services\TripayService;
use Mockery;
use PHPUnit\Framework\TestCase;

class TripayServiceTest extends TestCase
{
    private TripayServerInterface $serverService;
    private TripayBalanceInterface $balanceService;
    private TripayPrepaidCategoryInterface $prepaidCategoryService;
    private TripayService $tripayService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serverService = Mockery::mock(TripayServerInterface::class);
        $this->balanceService = Mockery::mock(TripayBalanceInterface::class);
        $this->prepaidCategoryService = Mockery::mock(TripayPrepaidCategoryInterface::class);

        $this->tripayService = new TripayService(
            serverService: $this->serverService,
            balanceService: $this->balanceService,
            prepaidCategoryService: $this->prepaidCategoryService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_check_server_delegates_to_server_service(): void
    {
        $expectedResponse = new ServerResponseDto(
            success: true,
            message: 'Server online',
            data: 1
        );

        $this->serverService
            ->shouldReceive('checkServer')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->tripayService->checkServer();

        $this->assertSame($expectedResponse, $result);
        $this->assertInstanceOf(ServerResponseDto::class, $result);
    }

    public function test_check_balance_delegates_to_balance_service(): void
    {
        $expectedResponse = new BalanceResponseDto(
            success: true,
            message: 'Balance available',
            data: 1000000
        );

        $this->balanceService
            ->shouldReceive('checkBalance')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->tripayService->checkBalance();

        $this->assertSame($expectedResponse, $result);
        $this->assertInstanceOf(BalanceResponseDto::class, $result);
    }

    public function test_server_returns_server_service_instance(): void
    {
        $result = $this->tripayService->server();

        $this->assertSame($this->serverService, $result);
        $this->assertInstanceOf(TripayServerInterface::class, $result);
    }

    public function test_balance_returns_balance_service_instance(): void
    {
        $result = $this->tripayService->balance();

        $this->assertSame($this->balanceService, $result);
        $this->assertInstanceOf(TripayBalanceInterface::class, $result);
    }

    public function test_can_chain_methods_through_service_instances(): void
    {
        $serverResponse = new ServerResponseDto(
            success: true,
            message: 'Server online',
            data: 1
        );

        $balanceResponse = new BalanceResponseDto(
            success: true,
            message: 'Balance available',
            data: 5000000
        );

        $this->serverService
            ->shouldReceive('checkServer')
            ->once()
            ->andReturn($serverResponse);

        $this->balanceService
            ->shouldReceive('checkBalance')
            ->once()
            ->andReturn($balanceResponse);

        // Test chaining through service instances
        $serverResult = $this->tripayService->server()->checkServer();
        $balanceResult = $this->tripayService->balance()->checkBalance();

        $this->assertSame($serverResponse, $serverResult);
        $this->assertSame($balanceResponse, $balanceResult);
    }
}