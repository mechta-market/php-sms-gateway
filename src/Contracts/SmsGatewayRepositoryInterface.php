<?php

namespace MechtaMarket\SmsGateway\Contracts;

use MechtaMarket\HttpClient\Response;

/**
 * Interface SmsGatewayRepositoryInterface
 * @package MechtaMarket\SmsGateway\Contracts
 */
interface SmsGatewayRepositoryInterface
{
    public function send(string $phone, string $text, bool $sync = false): Response;
}
