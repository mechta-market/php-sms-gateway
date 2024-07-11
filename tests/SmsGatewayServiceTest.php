<?php

namespace MechtaMarket\SmsGateway\Tests;

use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\SmsGatewayService;
use MechtaMarket\SmsGateway\Contracts\SmsGatewayRepositoryInterface;
use MechtaMarket\SmsGateway\Exceptions\SmsGatewayClientException;
use MechtaMarket\SmsGateway\Exceptions\SmsGatewayServerException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Exception;

/**
 * Class SmsGatewayServiceTest
 * @package MechtaMarket\SmsGateway\Tests
 */
class SmsGatewayServiceTest extends TestCase
{
    private MockObject $sms_gateway_repository_mock;
    private SmsGatewayService $sms_gateway_service;

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->sms_gateway_repository_mock = $this->createMock(SmsGatewayRepositoryInterface::class);
        $this->sms_gateway_service = new SmsGatewayService();
        $this->sms_gateway_service->setSmsGatewayRepository($this->sms_gateway_repository_mock);
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     * @throws \PHPUnit\Framework\MockObject\Exception
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
     * @throws SmsGatewayServerException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendSyncFailureClientError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request'
        ]);
        $response_mock->method('clientError')->willReturn(true);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', true)
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayClientException::class);
        $this->expectExceptionMessage('Bad Request');
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws SmsGatewayClientException
     */
    public function testSendSyncFailureServerError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('json')->willReturn([
            'desc' => 'Internal Server Error'
        ]);
        $response_mock->method('serverError')->willReturn(true);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', true)
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayServerException::class);
        $this->expectExceptionMessage('Internal Server Error');
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     * @throws \PHPUnit\Framework\MockObject\Exception
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
     * @throws SmsGatewayServerException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendAsyncFailureClientError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request'
        ]);
        $response_mock->method('clientError')->willReturn(true);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', false)
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayClientException::class);
        $this->expectExceptionMessage('Bad Request');
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }

    /**
     * @throws SmsGatewayClientException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSendAsyncFailureServerError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('json')->willReturn([
            'desc' => 'Internal Server Error'
        ]);
        $response_mock->method('serverError')->willReturn(true);

        $this->sms_gateway_repository_mock->method('send')
            ->with('1234567890', 'Test message', false)
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayServerException::class);
        $this->expectExceptionMessage('Internal Server Error');
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }
}
