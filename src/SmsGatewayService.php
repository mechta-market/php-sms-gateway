<?php

namespace MechtaMarket\SmsGateway;

use InvalidArgumentException;
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

    public function setClient(HttpClient $client): void
    {
        $this->client = $client;
    }

    public function getClient(): HttpClient
    {
        return $this->client;
    }

    /**
     * @throws SmsGatewayClientException
     * @throws SmsGatewayServerException
     */
    public function sendSync(string $phone, string $text): int
    {
        $phone = $this->checkPhoneAndFormatNumber($phone);
        $this->checkText($text);

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
        $phone = $this->checkPhoneAndFormatNumber($phone);
        $this->checkText($text);

        $response = $this->send($phone, $text, false);
        $this->handleResponse($response);
    }

    private function send(string $phone, string $text, bool $sync): Response
    {
        return $this->getClient()->asJson()->post('send', [
            'to' => $phone,
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
            if ($response->body()) {
                throw new SmsGatewayServerException(
                    'Failed to send SMS with response: ' . $response->body(),
                    $response->status()
                );
            }
            throw new SmsGatewayServerException(
                'Failed to send SMS with empty response',
                $response->status()
            );
        }
    }

    private function checkPhoneAndFormatNumber(string $phone): string
    {
        if (empty($phone)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10) {
            throw new InvalidArgumentException('Phone number is too short');
        }

        return substr(trim($phone), -10);
    }

    private function checkText(string $text): void
    {
        if (empty($text) || empty(trim($text))) {
            throw new InvalidArgumentException('Text cannot be empty');
        }
    }

    private function initClient($base_url): void
    {
        $this->setClient(new HttpClient());
        $this->getClient()->baseUrl($base_url);
    }
}
