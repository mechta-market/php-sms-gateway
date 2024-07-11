<?php

namespace MechtaMarket\SmsGateway\Tests;

use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\Contracts\SmsGatewayRepositoryInterface;
use MechtaMarket\SmsGateway\SmsGatewayService;
use PHPUnit\Framework\TestCase;
use Exception;

/**
 * Class SmsGatewayServiceTest
 * @package MechtaMarket\SmsGateway\Tests
 */
class SmsGatewayServiceTest extends TestCase
{
    private SmsGatewayRepositoryInterface $sms_gateway_repository_mock;
    private SmsGatewayService $sms_gateway_service;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->sms_gateway_repository_mock = $this->createMock(SmsGatewayRepositoryInterface::class);
        $this->sms_gateway_service = new SmsGatewayService();
        $this->sms_gateway_service->setSmsGatewayRepository($this->sms_gateway_repository_mock);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testSendSyncSuccess(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(200);
        $response_mock->method('json')->willReturn(['id' => 1]);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', true)
            ->willReturn($response_mock);

        $result = $this->sms_gateway_service->sendSync('1234567890', 'Test message');
        $this->assertEquals(1, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendSyncFailureStatus400(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request',
            'error_code' => '400'
        ]);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', true)
            ->willReturn($response_mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send SMS');
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendSyncFailureStatus500(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('json')->willReturn([
            'desc' => 'Internal Server Error',
            'error_code' => '500'
        ]);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', true)
            ->willReturn($response_mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send SMS');
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function testSendAsyncSuccess(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(200);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', false)
            ->willReturn($response_mock);

        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
        $this->assertTrue(true); // Если исключения не было, тест прошел успешно
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendAsyncFailureStatus400(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request',
            'error_code' => '400'
        ]);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', false)
            ->willReturn($response_mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send SMS');
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendAsyncFailureStatus500(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('json')->willReturn([
            'desc' => 'Internal Server Error',
            'error_code' => '500'
        ]);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', false)
            ->willReturn($response_mock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to send SMS');
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }
}
