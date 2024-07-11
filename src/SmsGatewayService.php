<?php

namespace MechtaMarket\SmsGateway;

use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\Contracts\SmsGatewayRepositoryInterface;
use MechtaMarket\SmsGateway\Exceptions\{
    SmsGatewayClientException,
    SmsGatewayServerException
};
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
     * @throws SmsGatewayClientException
     * @throws SmsGatewayServerException
     */
    public function sendSync(string $phone, string $text): int
    {
        $response = $this->send($phone, $text, true);
        $this->handleResponse($response);

        $body = $response->json();

        if (!isset($body['id'])) {
            throw new SmsGatewayServerException('Failed to send SMS', 'no_id');
        }

        return (int) $body['id'];
    }

    /**
     * @throws SmsGatewayClientException
     * @throws SmsGatewayServerException
     */
    public function sendAsync(string $phone, string $text): void
    {
        $response = $this->send($phone, $text, false);
        $this->handleResponse($response);
    }

    public function send(string $phone, string $text, bool $sync): Response
    {
        return $this->getSmsGatewayRepository()->send($phone, $text, $sync);
    }

    /**
     * @throws SmsGatewayClientException
     * @throws SmsGatewayServerException
     */
    private function handleResponse(Response $response): void
    {
        if ($response->clientError()) {
            $body = $response->json();
            throw new SmsGatewayClientException(
                $body['desc'] ?? 'Client error occurred',
                $body['error_code'] ?? 'unknown',
                $response->status()
            );
        }

        if ($response->serverError()) {
            $body = $response->json();
            throw new SmsGatewayServerException(
                $body['desc'] ?? 'Server error occurred',
                $body['error_code'] ?? 'unknown',
                $response->status()
            );
        }
    }
}
