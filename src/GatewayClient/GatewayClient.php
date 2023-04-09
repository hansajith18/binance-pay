<?php

namespace CryptoPay\Binancepay\GatewayClient;


use CryptoPay\Binancepay\GatewayClientConfig\ClientConfig;
use CryptoPay\Binancepay\GatewayClientUtils\HmacUtils;
use CryptoPay\Binancepay\GatewayClientUtils\RestClient;
use JsonException;

class GatewayClient
{

    protected ClientConfig $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @throws JsonException
     */
    public function init($request)
    {
        return $this->process($request);
    }

    /**
     * @throws JsonException
     */
    protected function process($request)
    {
        $jsonRequest = $this->buildRequest($request);

        $headers = $this->buildHeaders($jsonRequest);

        $jsonResponse = RestClient::sendRequest($this->config, $jsonRequest, $headers);

        return $this->buildResponse($jsonResponse);
    }

    private function buildHeaders($json_request): array
    {

        $timestamp = round(microtime(true) * 1000);
        $payload = $timestamp . "\n" . $this->config->getNonce() . "\n" . $json_request . "\n";

        $binance_pay_key = $this->config->getBinancePayKey();
        $binance_pay_secret = $this->config->getBinancePaySecret();

        $signature = HmacUtils::generateHmac($payload, $binance_pay_secret);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "BinancePay-Timestamp: $timestamp";
        $headers[] = "BinancePay-Nonce: " . $this->config->getNonce();
        $headers[] = "BinancePay-Certificate-SN: $binance_pay_key";
        $headers[] = "BinancePay-Signature: $signature";

        return $headers;
    }

    /**
     * @throws JsonException
     */
    private function buildRequest($requestData): bool|string
    {
        return json_encode($requestData, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    private function buildResponse($response)
    {
        return json_decode($response, TRUE, 512, JSON_THROW_ON_ERROR);
    }

}
