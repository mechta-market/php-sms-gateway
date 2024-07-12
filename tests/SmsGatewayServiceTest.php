<?php

namespace MechtaMarket\SmsGateway\Tests;

use MechtaMarket\HttpClient\{
    HttpClient,
    Response
};
use MechtaMarket\SmsGateway\SmsGatewayService;
use MechtaMarket\SmsGateway\Exceptions\{
    SmsGatewayClientException,
    SmsGatewayServerException
};
use PHPUnit\Framework\{MockObject\Exception, TestCase, MockObject\MockObject};

/**
 * Class SmsGatewayServiceTest
 * @package MechtaMarket\SmsGateway\Tests
 */
class SmsGatewayServiceTest extends TestCase
{
    private MockObject $http_client_mock;
    private SmsGatewayService $sms_gateway_service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->http_client_mock = $this->createMock(HttpClient::class);
        $this->sms_gateway_service = new SmsGatewayService();
        $this->sms_gateway_service->setClient($this->http_client_mock);
    }

    /**
     * @throws SmsGatewayServerException
     * @throws Exception
     * @throws SmsGatewayClientException
     */
    public function testSendSyncSuccess(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(200);
        $response_mock->method('json')->willReturn(['id' => 1]);
        $response_mock->method('successful')->willReturn(true);
        $response_mock->method('clientError')->willReturn(false);
        $response_mock->method('serverError')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => true
            ])
            ->willReturn($response_mock);

        $result = $this->sms_gateway_service->sendSync('1234567890', 'Test message');
        $this->assertEquals(1, $result);
    }

    /**
     * @throws SmsGatewayServerException
     * @throws Exception
     */
    public function testSendSyncFailureClientError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request'
        ]);
        $response_mock->method('clientError')->willReturn(true);
        $response_mock->method('serverError')->willReturn(false);
        $response_mock->method('successful')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => true
            ])
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayClientException::class);
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws SmsGatewayClientException
     * @throws Exception
     */
    public function testSendSyncFailureServerError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('body')->willReturn('Internal Server Error');
        $response_mock->method('clientError')->willReturn(false);
        $response_mock->method('serverError')->willReturn(true);
        $response_mock->method('successful')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => true
            ])
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayServerException::class);
        $this->sms_gateway_service->sendSync('1234567890', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     * @throws Exception
     */
    public function testSendAsyncSuccess(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(200);
        $response_mock->method('successful')->willReturn(true);
        $response_mock->method('clientError')->willReturn(false);
        $response_mock->method('serverError')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => false
            ])
            ->willReturn($response_mock);

        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
        $this->assertTrue(true); // Если исключения не было, тест прошел успешно
    }

    /**
     * @throws SmsGatewayServerException
     * @throws Exception
     */
    public function testSendAsyncFailureClientError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(400);
        $response_mock->method('json')->willReturn([
            'desc' => 'Bad Request'
        ]);
        $response_mock->method('clientError')->willReturn(true);
        $response_mock->method('serverError')->willReturn(false);
        $response_mock->method('successful')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => false
            ])
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayClientException::class);
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }

    /**
     * @throws Exception
     * @throws SmsGatewayClientException
     */
    public function testSendAsyncFailureServerError(): void
    {
        $response_mock = $this->createMock(Response::class);
        $response_mock->method('status')->willReturn(500);
        $response_mock->method('body')->willReturn('Internal Server Error');
        $response_mock->method('clientError')->willReturn(false);
        $response_mock->method('serverError')->willReturn(true);
        $response_mock->method('successful')->willReturn(false);

        $this->http_client_mock->method('asJson')->willReturn($this->http_client_mock);
        $this->http_client_mock->method('post')
            ->with('send', [
                'phone' => '1234567890',
                'text' => 'Test message',
                'sync' => false
            ])
            ->willReturn($response_mock);

        $this->expectException(SmsGatewayServerException::class);
        $this->sms_gateway_service->sendAsync('1234567890', 'Test message');
    }
}
