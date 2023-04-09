<?php

namespace CryptoPay\Binancepay\GatewayClientConfig;

use Exception;

class ClientConfig
{

    private string $serviceBaseUrl;

    private string $serviceEndpoint;

    private string $nonce;

    private string $binance_pay_key;

    private string $binance_pay_secret;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->serviceBaseUrl = config('binancepay.binance_service_base_url', 'https://bpay.binanceapi.com');

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nonce = '';
        for ($i = 1; $i <= 32; $i++) {
            $pos = random_int(0, strlen($chars) - 1);
            $char = $chars[$pos];
            $nonce .= $char;
        }
        $this->nonce = $nonce;

    }

    public function getServiceEndpoint()
    {
        return $this->serviceEndpoint;
    }

    public function setServiceEndpoint($endpoint): void
    {
        $this->serviceEndpoint = $this->serviceBaseUrl . "/" . $endpoint;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function setNonce($nonce): void
    {
        $this->nonce = $nonce;
    }

    public function getBinancePayKey()
    {
        return $this->binance_pay_key;
    }

    public function setBinancePayKey($binance_pay_key): void
    {
        $this->binance_pay_key = $binance_pay_key;
    }

    public function getBinancePaySecret()
    {
        return $this->binance_pay_secret;
    }

    public function setBinancePaySecret($binance_pay_secret): void
    {
        $this->binance_pay_secret = $binance_pay_secret;
    }


}
