<?php

namespace MechtaMarket\SmsGateway;

use MechtaMarket\HttpClient\HttpClient;
use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\Exceptions\{
    SmsGatewayClientException,
    SmsGatewayServerException
};

/**
 * Class SmsGatewayService
 * @package MechtaMarket\SmsGateway
 */
class SmsGatewayService
{
    private HttpClient $client;

    public function __construct(
        string $base_url = ''
    )
    {
        $this->initClient($base_url);
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

    private function send(string $phone, string $text, bool $sync): Response
    {
        return $this->getClient()->asJson()->post('send', [
            'phone' => $phone,
            'text' => $text,
            'sync' => $sync
        ]);
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

    private function initClient($base_url): void
    {
        $this->setClient(new HttpClient());
        $this->getClient()->baseUrl($base_url);
    }

    public function setClient(HttpClient $client): void
    {
        $this->client = $client;
    }

    public function getClient(): HttpClient
    {
        return $this->client;
    }
}
