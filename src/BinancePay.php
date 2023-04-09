<?php

namespace CryptoPay\Binancepay;

use CryptoPay\Binancepay\GatewayClient\GatewayClient;
use CryptoPay\Binancepay\GatewayClientConfig\ClientConfig;
use Exception;

/**
 * @author Navod Hansajith
 */
class BinancePay
{
    private ClientConfig $clientConfig;

    private array $request = [];

    private array $response = [];

    /**
     * Terminal type of which the merchant service applies to. Valid values are:
     * APP:
     * The client-side terminal type is a mobile application.
     *
     * WEB:
     * The client-side terminal type is a website that is opened via a PC browser.
     *
     * WAP:
     * The client-side terminal type is an HTML page that is opened via a mobile browser.
     *
     * MINI_PROGRAM:
     * The terminal type of the merchant side is a mini program on the mobile phone.
     *
     * OTHERS:
     * other undefined type
     *
     */
    private string $terminalType;

    /**
     * order currency in upper case.
     * only "BUSD","USDT","MBOX" can be accepted, fiat NOT supported.
     */
    private string $currency;

    /**
     * 0000: Electronics & Computers
     * 1000: Books, Music & Movies
     * 2000: Home, Garden & Tools
     * 3000: Clothes, Shoes & Bags
     * 4000: Toys, Kids & Baby
     * 5000: Automotive & Accessories
     * 6000: Game & Recharge
     * 7000: Entertainament & Collection
     * 8000: Jewelry
     * 9000: Domestic service
     * A000: Beauty care
     * B000: Pharmacy
     * C000: Sports & Outdoors
     * D000: Food, Grocery & Health products
     * E000: Pet supplies
     * F000: Industry & Science
     * Z000: Others
     */
    private string $goodsCategory;

    /**
     * the type of the goods for the order:
     * 01: Tangible Goods
     * 02: Virtual Goods
     */
    private string $goodsType;

    /**
     * The URL to redirect to when the payment is successful.
     */
    private string $returnUrl;

    /**
     * The URL to redirect to when payment is failed.
     */
    private string $cancelUrl;

    /**
     * orderExpireTime determines how long an order is valid for. If not specified, orderExpireTime will be 1 hour;
     * maximum orderExpireTime is 1 hour. Please input in milliseconds.
     */
    private ?int $orderExpireTime;

    /**
     * SupportPayCurrency determines the currencies that a customer is allowed to use to pay for the order.
     * If not specified, all Binance Pay supported currencies will be allowed.
     * Input to be separated by commas, e.g. "BUSD,BNB"
     */
    private string $supportPayCurrency = "USDT";

    /**
     * The URL for order notification, can only start with http or https.
     * If the webhookUrl is passed in the parameter, the webhook url configured on the merchant platform will not take effect,
     * and the currently passed url will be called back first.
     */
    private string $webhookUrl;

    /**
     * @throws Exception
     */
    public function __construct(string $endpoint)
    {
        $this->clientConfig = new ClientConfig();
        $this->clientConfig->setServiceEndpoint($endpoint);
        $this->clientConfig->setBinancePayKey(config('binancepay.binance_merchant_api_key'));
        $this->clientConfig->setBinancePaySecret(config('binancepay.binance_merchant_secret_key'));
        $this->terminalType = config('binancepay.binance_terminal_type', 'WEB');
        $this->currency = config('binancepay.binance_currency', "USDT");
        $this->goodsCategory = config('binancepay.binance_goods_category', "Z000");
        if (config('binancepay.binance_order_expire_time', 60) < 60) {
            $this->orderExpireTime = (time() + (config('binancepay.binance_order_expire_time', 59) * 60)) * 1000;
        }else{
            $this->orderExpireTime = null;
        }
        $this->returnUrl = config('binancepay.binance_service_return_url');
        $this->cancelUrl = config('binancepay.binance_service_cancel_url');
        $this->webhookUrl = config('binancepay.binance_service_webhook_url');

        $this->goodsType = config('binancepay.binance_goods_type', "02");
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    public function createOrder($data)
    {
        try {
            $client = new GatewayClient($this->clientConfig);

            $this->request = [
                "env" => [
                    "terminalType" => $this->terminalType
                ],
                "merchantTradeNo" => $data['merchant_trade_no'],
                "orderAmount" => $data['order_amount'],
                "currency" => $this->currency,
                "orderExpireTime" => $this->orderExpireTime,
                "returnUrl" => $this->returnUrl . "?trx-id=" . $data['trx_id'],
                "cancelUrl" => $this->cancelUrl . "?trx-id=" . $data['trx_id'],
                "webhookUrl" => $this->webhookUrl,
                "supportPayCurrency" => $this->supportPayCurrency,
                "goods" => [
                    "goodsType" => $this->goodsType,
                    "goodsCategory" => $this->goodsCategory,
                    "referenceGoodsId" => $data['package_id'],
                    "goodsName" => $data['goods_name'],
                    "goodsDetail" => $data['goods_detail'] ?? null
                ],
                "buyer" => $data['buyer']
            ];
            return $client->init($this->request);
        } catch (Exception $e) {

            $this->response['status'] = false;
            $this->response['code'] = 500;
            $this->response['errorMessage'] = $e->getMessage();

            return $this->response;
        }
    }

    public function query($data)
    {
        try {
            return (new GatewayClient($this->clientConfig))->init($data);
        } catch (Exception $e) {

            $this->response['status'] = false;
            $this->response['code'] = 500;
            $this->response['errorMessage'] = $e->getMessage();

            return $this->response;
        }
    }
}