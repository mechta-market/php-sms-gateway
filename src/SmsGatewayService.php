<?php

namespace MechtaMarket\SmsGateway;

use Exception;
use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\Contracts\SmsGatewayRepositoryInterface;
use MechtaMarket\SmsGateway\Repositories\SmsGatewayRepository;

/**
 * Class SmsGatewayService
 * @package MechtaMarket\SmsGateway
 */
class SmsGatewayService
{
    private SmsGatewayRepositoryInterface $sms_gateway_repository;

    public function __construct(string $base_url = '')
    {
        $sms_gateway_repository = new SmsGatewayRepository($base_url);
        $this->setSmsGatewayRepository($sms_gateway_repository);
    }

    public function setSmsGatewayRepository(SmsGatewayRepositoryInterface $sms_gateway_repository): void
    {
        $this->sms_gateway_repository = $sms_gateway_repository;
    }

    public function getSmsGatewayRepository(): SmsGatewayRepositoryInterface
    {
        return $this->sms_gateway_repository;
    }

    /**
     * @throws Exception
     */
    public function sendSync(string $phone, string $text): int
    {
        $response = $this->send($phone, $text, true);

        if ($response->status() !== 200) {
            throw new Exception('Failed to send SMS', $response->status());
        }

        $body = $response->json();

        if (!isset($body['id'])) {
            throw new Exception('Failed to send SMS');
        }

        return (int) $body['id'];
    }

    /**
     * @throws Exception
     */
    public function sendAsync(string $phone, string $text): void
    {
        $response = $this->send($phone, $text, false);

        if ($response->status() !== 200) {
            throw new Exception('Failed to send SMS', $response->status());
        }
    }

    /**
     * @throws Exception
     */
    public function send(string $phone, string $text, bool $sync): Response
    {
        return $this->getSmsGatewayRepository()->send($phone, $text, $sync);
    }
}
