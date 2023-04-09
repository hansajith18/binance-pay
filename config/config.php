<?php

return [
    'binance_service_base_url' => env('BINANCE_SERVICE_BASE_URL', 'https://bpay.binanceapi.com'),

    'binance_service_return_url' => env('BINANCE_SERVICE_RETURN_URL'),

    'binance_service_cancel_url' => env('BINANCE_SERVICE_CANCEL_URL'),

    /*
     * orderExpireTime determines how long an order is valid for.
     * If not specified, orderExpireTime will be 1 hour;
     * maximum orderExpireTime is 1 hour.
     * Please input in Minutes.
     * */
    'binance_order_expire_time' => 5, // in minutes, max 60

    'binance_service_webhook_url' => env('BINANCE_SERVICE_WEBHOOK_URL'),

    'binance_merchant_api_key' => env('BINANCE_MERCHANT_API_KEY'),

    'binance_merchant_secret_key' => env('BINANCE_MERCHANT_SECRET_KEY'),

    'binance_terminal_type' => 'WEB',

    'binance_currency' => 'USDT',

     /**
     * the type of the goods for the order:
     * 01: Tangible Goods
     * 02: Virtual Goods
     */

    'binance_goods_type' => '02',

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

    'binance_goods_category' => 'Z000',
];