<?php

namespace MechtaMarket\SmsGateway\Repositories;

use MechtaMarket\HttpClient\HttpClient;
use MechtaMarket\HttpClient\Response;
use MechtaMarket\SmsGateway\Contracts\SmsGatewayRepositoryInterface;

/**
 * Class SmsGatewayRepository
 * @package MechtaMarket\SmsGateway\Repositories
 */
class SmsGatewayRepository implements SmsGatewayRepositoryInterface
{
    const METHOD_SEND = 'send';

    private HttpClient $client;

    public function __construct(string $base_url)
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

    public function send(string $phone, string $text, bool $sync = false): Response
    {
        return $this->getClient()->bodyFormat('json')->post(self::METHOD_SEND, [
            'to' => $phone,
            'text' => $text,
            'sync' => $sync
        ]);
    }

    private function initClient(string $base_url): void
    {
        $client = new HttpClient();
        $client->baseUrl($base_url);

        $this->setClient($client);
    }
}
