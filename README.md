![Binance Pay API](https://github.com/hansajith18/repo-assets/raw/master/crypto-pay/binancepay-header.png)

# Binance Pay API for Laravel
Binance Pay API for PHP and Laravel - This is a simple and quick package on how to initiate crypto payments using the Official Binance API. You can use this to initiate ecommerce payments or any other payments of your choose from your website. 

## Binance Pay API
To authenticate requests in the Binance Pay API, API keys are used. You can view and manage your API keys in the Binance Merchant Admin Portal.

Since API keys carry numerous privileges, it is essential to keep them secure. Avoid sharing your secret API keys in publicly accessible places like GitHub, client-side code, and similar locations.

It is mandatory to make all API requests over HTTPS. Calls made over plain HTTP will not succeed. Also, requests without authentication will fail.

## Installation

You can install the package via composer:

```
composer require crypto-pay/binancepay
```

## Get API Credentials#
Binance uses the Binance Pay API keys to authenticate API requests. You can view and manage your API keys in the [Binance Merchant Admin Portal](https://merchant.binance.com/en/).

Go to: Developers → Settings → API Keys → Generate API Key

[Read more...](https://merchant.binance.com/en/docs/getting-started#get-api-credentials)

### Configurations

```dotenv
BINANCE_SERVICE_BASE_URL=https://bpay.binanceapi.com
BINANCE_SERVICE_RETURN_URL="/binancepay/returnUrl" # The URL to redirect to when the payment is successful.
BINANCE_SERVICE_CANCEL_URL="/binancepay/cancelURL" # The URL to redirect to when payment is failed.
BINANCE_SERVICE_WEBHOOK_URL="webhook-url"
BINANCE_MERCHANT_API_KEY=<api-key>
BINANCE_MERCHANT_SECRET_KEY=<secret-key>
```

### Base Url

```
https://bpay.binanceapi.com
```

## Create Order API#
You can use the “Create Order” API to initiate an order. You will receive a checkoutURL in the API response. Redirect users to the checkoutURL and they can complete the payment on our Binance hosted checkout page.

### Endpoint
```
POST /binancepay/openapi/v2/order
```

### Request Example
For full API specification, please go to 
[API Request Reference.](https://developers.binance.com/docs/binance-pay/api-order-create-v2#request-parameters)

```json
{
  "env" : {
    "terminalType": "APP"
  },
  "merchantTradeNo": "9825382937292",
  "orderAmount": 25.17,
  "currency": "BUSD",
  "goods" : {
    "goodsType": "01",
    "goodsCategory": "D000",
    "referenceGoodsId": "7876763A3B",
    "goodsName": "Ice Cream",
    "goodsDetail": "Greentea ice cream cone"
  }
}
```

 
### Response Example

```json
{
  "status": "SUCCESS",
  "code": "000000",
  "data": {
    "prepayId": "29383937493038367292",
    "terminalType": "APP",
    "expireTime": 121123232223,
    "qrcodeLink": "https://qrservice.dev.com/en/qr/dplkb005181944f84b84aba2430e1177012b.jpg",
    "qrContent": "https://qrservice.dev.com/en/qr/dplk12121112b",
    "checkoutUrl": "https://pay.binance.com/checkout/dplk12121112b",
    "deeplink": "bnc://app.binance.com/payment/secpay/xxxxxx",
    "universalUrl": "https://app.binance.com/payment/secpay?_dp=xxx=&linkToken=xxx"
  },
  "errorMessage": ""
}
```

### USAGE 

```php
    // payment controller
    
    use CryptoPay\Binancepay\BinancePay;
    
    public function initiateBinancePay(Request $request){
        
        // Other logics 
        
        $user = Auth::user();
        $product = Product::find(100); // Use your own product
         
        $data['order_amount'] =  25.17;
        $data['package_id'] = $product->id; // referenceGoodsId: id from the DB Table that user choose to purchase 
        $data['goods_name'] = $product->name;
        $data['goods_detail'] = null;
        $data['buyer'] = [
            "referenceBuyerId" => $user->id,
            "buyerEmail" => $user->email,
            "buyerName" => [
                "firstName" => $user->first_name,
                "lastName" => $user->last_name
            ]
        ];
        $data['trx_id'] = $transaction->id; // used to identify the transaction after payment has been processed
        $data['merchant_trade_no'] = '9825382937292' // Provide an unique code;
    
    
        $transaction = Transaction::create([
            'user_id' => $user->id, 
            'package_id' => $product->id,
            'merchant_trade_no' => $data['merchant_trade_no'],
            'currency' => "USDT",
            'amount' => $product->amount
            // others  
        ]);
        
        $binancePay = new BinancePay("binancepay/openapi/v2/order");
        $res = $binancePay->createOrder($data);
        
        if ($result['status'] === 'SUCCESS') {
          // DO YOUR MAGIC. ITS UPTO YOU 😎
        }
        
        // FAIL
        
        // Other logics 
    }
```

## Query Order#
You can use the "Query Order" API to query an order and check the order status in real time.

### Endpoint
```
POST   /binancepay/openapi/v2/order/query
```
### Request Example
For full API specification, please go to [API Request Reference](https://developers.binance.com/docs/binance-pay/api-order-query#request-parameters).

```json
{
  "merchantTradeNo": "9825382937292"
}

```
### Response Example
For full API specification, please go to [API Request Reference.](https://developers.binance.com/docs/binance-pay/api-order-query#response-parameters)

```json
    {
      "status": "SUCCESS",
      "code": "000000",
      "data": {
        "merchantId": "987321472",
        "prepayId": "29383937493038367292",
        "transactionId": "23729202729220282",
        "merchantTradeNo": "9825382937292",
        "tradeType": "APP",
        "status": "PAID",
        "currency": "BUSD",
        "totalFee": 10.88,
        "productName": "Ice Cream",
        "productDetail": "Green Tea ice cream cone",
        "transactTime": "1425744000123",
        "createTime": "1425744000000"
      },
      "errorMessage": ""
    }
```

You can monitor the payment status using the parameter "status".

| Status      | Description                                                                                           |
|-------------|-------------------------------------------------------------------------------------------------------|
| `INITIAL`   | The transaction has been initiated. Return this status after calling `Create Order` API successfully. |
| `PENDING`   | The transaction is pending for payment.                                                               |
| `PAID`      | The transaction has been paid.                                                                        |
| `CANCELED`  | The transaction has been closed by you with the `close order` API.                                    |
| `ERROR`     | There is an error occurred during the transaction.                                                    |
| `REFUNDING` | The transaction is under a refund process.                                                            |
| `REFUNDED`  | The transaction is under a refund process.                                                            |
| `EXPIRED`   | The transaction is expired. By default, the QR code will expire after 1 hour.                         |

### USAGE

### Return & Cancel URL

```php
    use CryptoPay\Binancepay\BinancePay;

    // GET /binancepay/returnUrl
    public function returnCallback(Request $request)
    { 
        return $this->checkOrderStatus($request);
    }
    
    // GET /binancepay/cancelURL
    public function cancelCallback(Request $request)
    {
        return $this->checkOrderStatus($request);
    }
    
    private function checkOrderStatus(Request $request): RedirectResponse
    {  
        $transaction = Transaction::findOr($request->get('trx-id'), function () {
            // return redirect()->route('preferred-route')
            //                  ->with('warning', 'Something went wrong'); // show success invoice
        });

        $order_status = (new BinancePay("binancepay/openapi/v2/order/query"))
                            ->query(['merchantTradeNo' => $transaction->merchant_trade_no]);
           
        // Save transaction status or whatever you like according to the order status
        
        // $transaction->update(['status' => $order_status['data']['status']];
        // dd($order_status);
        
        // ITS UPTO YOU 😎
    }

```

# Binance Pay: Webhooks#
Binance uses webhooks to notify you when something happens. We will automatically send you a notification immediately to keep you up-to-date on your order status.

You will be able to configure webhook endpoints via the Merchant Management Platform.

## Setting up Webhooks#

For security purposes, Binance will add a signature for webhook notification. Merchants need to verify the signature using the public key issued from Binance Pay.

### How it works

1. Configure the webhook endpoints in the Merchant Management Platform (MMP).

2. Build the payload and verify the signature on your application. Please note that the certificate endpoint is fixed.

3. Identify the events you want to be notified of.

4. Get notified automatically on your order status.

5. After receiving the notification, please acknowledge it by returning a HTTP 200 and “SUCCESS” in the response body.

Read the [official documentation](https://merchant.binance.com/en/docs/functionalities/webhooks) for more details.

### Callback Webhook Endpoints#
Binance Pay will send order events with final status to partner for notification. You will be able to configure webhook endpoints via the [Binance Merchant Admin Portal](https://merchant.binance.com/en/dashboard). 

If you set the  WEBHOOK_ENDPOINT in the .env, it will override the default webhook endpoint in the Merchant admin portal.

```dotenv
 BINANCE_SERVICE_WEBHOOK_URL="/binancepay/webhook-url"
```

Result of the orders that are close will be notified to you through this webhook with bizStatus = `PAY_CLOSED`

> In situation where event was failed to send, the webhook will retry up to 6 times to resend the event.

### Verify Signatures `PHP`

#### Build the payload

```php
$payload = $headers['Binancepay-Timestamp'] . "\n" . $headers['Binancepay-Nonce'] . "\n" .
$entityBody . "\n";
```

#### Decode the Signature with Base64
```php
$decodedSignature = base64_decode ( $headers['Binancepay-Signature'] );
```

#### Verify the content with public key#
```php
openssl_verify($payload, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256 );
```

### Query Certificate
Merchants can use the “query certificate” API to obtain the public key and Hash Key.

#### End Point
```
POST   /binancepay/openapi/certificates
```

#### Response Parameters
| Attributes | Type   | Required | Description           |
|------------|--------|----------|-----------------------|
| certSerial | string | Y        | public key hash value |
| certPublic | string | Y        | public key            |

### Binance Pay: Order Notification

### Payload Example
You can either use “merchantTradeNo” or “prepayId” to search for a specific order. Please input at least one of them.

```json
    {
          "bizType": "PAY",
          "data": "{\"merchantTradeNo\":\"9825382937292\",\"totalFee\":0.88000000,\"transactTime\":1619508939664,\"currency\":\"BUSD\",\"openUserId\":\"1211HS10K81f4273ac031\",\"productType\":\"Food\",\"productName\":\"Ice Cream\",\"tradeType\":\"WEB\",\"transactionId\":\"M_R_282737362839373\"}",
          "bizId": 29383937493038367292,
          "bizStatus": "PAY_SUCCESS"
    }
```
To ensure that your server has received and accepted the notifications, please acknowledge every notification with a HTTP 200 and SUCCESS response in the body.

#### Response Example
Please ensure to send the appropriate response indicating a successful request. In case your process is successful but you fail to inform Binance Pay, it will continue to send notifications up to six times. However, if the process fails,  send returnCode as "FAIL," and Binance Pay will attempt to retry the operation. 

```json
    {
         "returnCode":"SUCCESS",
         "returnMessage":null
    }
```
Orders with no payment activity over 24 hours will be closed automatically, please contact Binance Merchant Operation (https://merchant.binance.com/en/help) in order to get the notification.

#### USAGE

```php
    use CryptoPay\Binancepay\BinancePay;


    public function callback(Request $request): JsonResponse
    {
        $webhookResponse = $request->all();
        $publicKey = (new BinancePay("binancepay/openapi/certificates"))
                        ->query($webhookResponse);
        try {
            if ($publicKey['status'] === "SUCCESS") {
            
                $payload = $request->header('Binancepay-Timestamp') . "\n" . 
                            $request->header('Binancepay-Nonce') . "\n" . 
                            json_encode($webhookResponse, JSON_THROW_ON_ERROR) . "\n";
                            
                $decodedSignature = base64_decode($request->header('Binancepay-Signature'));
            
                if (openssl_verify($payload, $decodedSignature, $publicKey['data'][0]['certPublic'], OPENSSL_ALGO_SHA256)) {
                    $merchantTradeNo = json_decode($webhookResponse['data'], true, 512, JSON_THROW_ON_ERROR)['merchantTradeNo'];
                    
                    $transaction = Transaction::where('merchant_trade_no', $merchantTradeNo)->firstOr(
                                    function () use ($merchantTradeNo) {
                                        throw new \RuntimeException("Order could not be found!: " . $merchantTradeNo);
                                    });
                                    
                    switch ($webhookResponse['bizStatus']) {
                        case "PAY_SUCCESS":
                            
                            // $order_status = (new BinancePay("binancepay/openapi/v2/order/query"))->query(compact('merchantTradeNo'));
                            
                            // DO YOUR MAGIC HERE PAYMENT IS SUCCESS 😎
                            break;
                        case "PAY_CLOSED":
                            // OHH... PAYMENT IS FAILED 🙁
                            break;
                    }
                    
                } else {
                    throw new \RuntimeException("Signature verification failed😒🤨");
                }
            } else {
                throw new \RuntimeException($publicKey["errorMessage"]);
            }
        }catch (Exception $e) {
            return response()->json(['returnCode' => 'FAIL', 'returnMessage' => $e->getMessage()], 200);
        }
        return response()->json(['returnCode' => 'SUCCESS', 'returnMessage' => null], 200);
    }
```

## Finally

> If you have any questions or feedback, please feel free to let us know by opening an issue. We value your thoughts and suggestions, and encourage you to contribute to the project by submitting a pull request.


# Upcoming Developments 😶‍🌫️👹

`C2B - Customer to Business`
- [x] Create Order/Initiate
- [ ] Create Order Refactoring with more customizations (v1.0.0 only USDT will accept).
- [x] Query Order
- [ ] Close Order
- [ ] Refund Order
- [ ] Query Refund Order
- [ ] Payment payer verification
- [ ] Transfer fund: Transfer Fund
- [ ] Transfer fund: Query Transfer Result
- [ ] Sub-merchant: Create SubMerchant
- [ ] Wallet Balance: Wallet Balance Query
- [ ] Wallet Balance: Wallet Balance Query V2
- [ ] Direct Debit: Direct Debit/Pre Authorization Create Contract
- [ ] Direct Debit: Direct Debit/Pre Authorization Query Contract
- [ ] Direct Debit: Direct Debit Payment Notification
- [ ] Direct Debit: Direct Debit/Pre Authorization Payment
- [ ] Payout: Batch Payout
- [ ] Payout: Payout Validate Receiver
- [ ] Payout: Payout Query
- [ ] Convert: List All Convert Pairs
- [ ] Convert: Send Quote
- [ ] Convert: Execute Quote
- [ ] Convert: Get Order Status
- [ ] Reporting: Download Report
- [ ] Reporting: Download Balance Report
- [ ] Profit Sharing: Add Receiver
- [ ] Profit Sharing: Query Receiver
- [ ] Profit Sharing: Delete Receiver
- [ ] Profit Sharing: Submit Split
- [ ] Profit Sharing: Query Split
- [ ] Share Info: Share Account id
- [x] Webhook Notification: Order Notification
- [ ] Webhook Notification: Payout Notification
- [ ] Webhook Notification: Refund Notification

`C2C - Customer to Customer`
- [ ] OAuth API: Query Supported Currency
- [ ] OAuth API: Create Collection
- [ ] Query Collection Result
- [ ] Webhook: Collection Notification

## Binance Pay Official Documentation
https://merchant.binance.com/en/docs/home <br>
https://developers.binance.com/docs/binance-pay/introduction

## Contribution Guidelines
Thank you for considering contributing to our Laravel package! We welcome all contributions and appreciate your time and effort in making this package better.

## Reporting Bugs
If you have encountered a bug or an issue with the package, please open a new issue on the GitHub repository with a clear and concise description of the problem, steps to reproduce it, and any relevant error messages or logs. Screenshots or code snippets that demonstrate the problem can also be helpful.

## Making Changes
If you would like to make changes to the package, please fork the repository and create a new branch for your changes. Before starting any work, please check the open issues and pull requests to make sure that someone else hasn't already addressed the issue or made similar changes.

Once you have made your changes, please create a pull request with a clear description of the changes, including any relevant code snippets or screenshots. If your changes are related to an open issue, please reference the issue in your pull request description.

### Code Standards
We follow the Laravel coding standards and PSR-12 coding style. Please make sure that your changes adhere to these standards.

### Testing
We have a test suite for the package, and we require that all changes are covered by tests. Please make sure that your changes have adequate test coverage and that all tests pass.

### Code of Conduct
We have a code of conduct that all contributors are expected to follow. Please read the code of conduct before contributing to the package.

### Credits
We appreciate all contributions and will give credit to all contributors in the package documentation. If you would like to be credited differently, please let us know in your pull request.

Thank you for your contributions!

## Contribution
- 💻👨‍💻👩‍💻 Pull requests are always welcome! We would love to see your contribution!
- ⭐️ Give us a star if you like our work! It would mean a lot to us!
- 🔥 Fork and Clone our repository! We promise, it's gonna be an awesome experience!
- 🚀 Select from our existing issues or create a new one and give us a PR with your bugfix or improvement! 
- ❤️ We are always excited to see new changes and improvements

> For any inquiries Email: hansajith18@gmail.com







