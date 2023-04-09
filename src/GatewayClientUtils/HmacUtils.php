<?php

namespace CryptoPay\Binancepay\GatewayClientUtils;

class HmacUtils
{

    private function __construct()
    {
    }

    public static function generateHmac($data, $secret): string
    {
        return strtoupper(hash_hmac('SHA512', utf8_decode($data), utf8_decode($secret), FALSE));
    }

}
