<?php

namespace CryptoPay\Binancepay\GatewayClientUtils;

class RestClient
{

    private function __construct()
    {
    }

    public static function sendRequest($config, $jsonRequest, $headers): bool|string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config->getServiceEndpoint());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

}
