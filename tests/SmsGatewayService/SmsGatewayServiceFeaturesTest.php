<?php

namespace MechtaMarket\SmsGateway\Tests\SmsGatewayService;

use MechtaMarket\HttpClient\{HttpClient, Response};
use InvalidArgumentException;
use MechtaMarket\SmsGateway\Exceptions\{SmsGatewayClientException, SmsGatewayServerException};
use MechtaMarket\SmsGateway\SmsGatewayService;
use PHPUnit\Framework\{MockObject\Exception, MockObject\MockObject, TestCase};

/**
 * Class SmsGatewayServiceTest
 * @package MechtaMarket\SmsGateway\Tests
 */
class SmsGatewayServiceFeaturesTest extends TestCase
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
    public function testSendSyncWithValidData(): void
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
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithEmptyPhone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithIncompletePhone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('+123', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithLettersInPhone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('12345abc', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithMixedLettersAndNumbersInPhone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('+123abc456', 'Test message');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithEmptyText(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('1234567890', '');
    }

    /**
     * @throws SmsGatewayServerException
     * @throws SmsGatewayClientException
     */
    public function testSendSyncWithSpacesOnlyText(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sms_gateway_service->sendSync('1234567890', '     ');
    }
}
