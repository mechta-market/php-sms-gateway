<?php

namespace MechtaMarket\SmsGateway\Exceptions;

use Exception;

class SmsGatewayClientException extends Exception
{
    protected $error_code;

    public function __construct($message, $error_code, $code = 0, Exception $previous = null)
    {
        $this->error_code = $error_code;
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }
}
