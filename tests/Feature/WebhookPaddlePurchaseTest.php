<?php


use Illuminate\Support\Carbon;
use Spatie\WebhookClient\Models\WebhookCall;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertSame;

it('can create a valid Paddle webhook signature', function () {
    // Arrange
    $originalTimestamp = 1718139311;
    [$originalArrBody, $originalSigHeader, $originalRawJsonBody] = getValidPaddleWebhookRequest();

    // Assert
    [$body, $header] = generateValidSignedPaddleWebhookRequest($originalArrBody, $originalTimestamp);
    assertSame(json_encode($body), $originalRawJsonBody);
    assertSame($header, $originalSigHeader);

});

it('stores a paddle purchase request', function () {
    // Arrange
    assertDatabaseCount(WebhookCall::class, 0);
    [$arrData] = getValidPaddleWebhookRequest();
    // We will have to generate a fresh signature because the timestamp cannot be older
    // than 5 seconds, or our webhook signature validator middleware will block the request
    [$requestBody, $requestHeaders] = generateValidSignedPaddleWebhookRequest($arrData);

    // Act & Assert
    // Act
    // needed to prevent the checkout url slashes from being escaped
    postJson('webhooks', $requestBody, $requestHeaders);
    // Assert
    assertDatabaseCount(WebhookCall::class, 1);

});

it('does not store invalid paddle purchase request', function () {
    // Arrange
    assertDatabaseCount(WebhookCall::class, 0);
    // Act
    post('webhooks', []);

    //Assert
    assertDatabaseCount(WebhookCall::class, 0);
});


it('dispatches a job for a valid paddle request', function () {
    //Arrage

    //Act & Assert
});

it('does not dispatch a job for invalid paddle request', function () {
    //Arrage

    //Act & Assert
});




function getValidPaddleWebhookRequest(): array
{
    $sigHeader = [
        'Paddle-Signature' => 'ts=1718139311;h1=c1381d8d4359b41fab3e87e172d86ae2baad8fd8cbd39f76c3c86717285c16d4'];
    $parsedData = [ "event_id" => "evt_01jhqxagvbe182n554m0abxtpk",
        "event_type" => "transaction.completed",
        "occurred_at" => "2025-01-16T15:57:14.987840Z",
        "notification_id" => "ntf_01jhqxah0avqch33fgf404tt6p",
        "data" => [
            "id" => "txn_01jhqx6wwbna235d0mc83sta2r",
            "items" => [
                [
                    "price" => [
                        "id" => "pri_01jhqsmk8f3z654fgsj7j20a5d",
                        "name" => "Pago Laravel For Beginners",
                        "type" => "standard",
                        "status" => "active",
                        "quantity" => [
                            "maximum" => 10000,
                            "minimum" => 1
                        ],
                        "tax_mode" => "account_setting",
                        "created_at" => "2025-01-16T14:52:50.831552Z",
                        "product_id" => "pro_01jhqsgcmrfcvr2bvnjay9enqp",
                        "unit_price" => [
                            "amount" => "1500",
                            "currency_code" => "USD"
                        ],
                        "updated_at" => "2025-01-16T14:52:50.831552Z",
                        "custom_data" => null,
                        "description" => "Pago unico",
                        "trial_period" => null,
                        "billing_cycle" => [
                            "interval" => "month",
                            "frequency" => 1
                        ],
                        "unit_price_overrides" => [
                        ]
                    ],
                    "price_id" => "pri_01jhqsmk8f3z654fgsj7j20a5d",
                    "quantity" => 1,
                    "proration" => null
                ]
            ],
            "origin" => "web",
            "status" => "completed",
            "details" => [
                "totals" => [
                    "fee" => "125",
                    "tax" => "260",
                    "total" => "1500",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "1115",
                    "subtotal" => "1240",
                    "grand_total" => "1500",
                    "currency_code" => "USD",
                    "credit_to_balance" => "0"
                ],
                "line_items" => [
                    [
                        "id" => "txnitm_01jhqx7vmvjstrn84kn2p62mh5",
                        "totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "item_id" => null,
                        "product" => [
                            "id" => "pro_01jhqsgcmrfcvr2bvnjay9enqp",
                            "name" => "Laravel For Beginners",
                            "type" => "standard",
                            "status" => "active",
                            "image_url" => null,
                            "created_at" => "2025-01-16T14:50:32.984Z",
                            "updated_at" => "2025-01-16T14:50:32.984Z",
                            "custom_data" => [
                                "Product" => "One"
                            ],
                            "description" => "Laravel For Beginners",
                            "tax_category" => "standard"
                        ],
                        "price_id" => "pri_01jhqsmk8f3z654fgsj7j20a5d",
                        "quantity" => 1,
                        "tax_rate" => "0.21",
                        "unit_totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "is_tax_exempt" => false,
                        "revised_tax_exempted" => false
                    ]
                ],
                "payout_totals" => [
                    "fee" => "125",
                    "tax" => "260",
                    "total" => "1500",
                    "credit" => "0",
                    "balance" => "0",
                    "discount" => "0",
                    "earnings" => "1115",
                    "fee_rate" => "0.05",
                    "subtotal" => "1240",
                    "grand_total" => "1500",
                    "currency_code" => "USD",
                    "exchange_rate" => "1",
                    "credit_to_balance" => "0"
                ],
                "tax_rates_used" => [
                    [
                        "totals" => [
                            "tax" => "260",
                            "total" => "1500",
                            "discount" => "0",
                            "subtotal" => "1240"
                        ],
                        "tax_rate" => "0.21"
                    ]
                ],
                "adjusted_totals" => [
                    "fee" => "125",
                    "tax" => "260",
                    "total" => "1500",
                    "earnings" => "1115",
                    "subtotal" => "1240",
                    "grand_total" => "1500",
                    "currency_code" => "USD"
                ]
            ],
            "checkout" => [
                "url" => "https://localhost?_ptxn=txn_01jhqx6wwbna235d0mc83sta2r"
            ],
            "payments" => [
                [
                    "amount" => "1500",
                    "status" => "captured",
                    "created_at" => "2025-01-16T15:57:09.77558Z",
                    "error_code" => null,
                    "captured_at" => "2025-01-16T15:57:12.328878Z",
                    "method_details" => [
                        "card" => [
                            "type" => "visa",
                            "last4" => "4242",
                            "expiry_year" => 2025,
                            "expiry_month" => 5,
                            "cardholder_name" => "El Vergon"
                        ],
                        "type" => "card"
                    ],
                    "payment_method_id" => "paymtd_01jhqxabqy522yxh0dfg9ta2m4",
                    "payment_attempt_id" => "b869f115-69e5-46f0-bb53-86a5ba55a0de",
                    "stored_payment_method_id" => "7e50cc9c-4c76-4c99-a86c-06a8af3f931d"
                ]
            ],
            "billed_at" => "2025-01-16T15:57:12.485064Z",
            "address_id" => "add_01jhqx7vbp0pk2jrb48j2tdxbd",
            "created_at" => "2025-01-16T15:55:16.285664Z",
            "invoice_id" => "inv_01jhqxaep3506h3sq3g6bkaez5",
            "updated_at" => "2025-01-16T15:57:14.636836657Z",
            "business_id" => null,
            "custom_data" => null,
            "customer_id" => "ctm_01jhqx7vay44920c96e13931wn",
            "discount_id" => null,
            "receipt_data" => null,
            "currency_code" => "USD",
            "billing_period" => [
                "ends_at" => "2025-02-16T15:57:12.328878Z",
                "starts_at" => "2025-01-16T15:57:12.328878Z"
            ],
            "invoice_number" => "10630-10001",
            "billing_details" => null,
            "collection_mode" => "automatic",
            "subscription_id" => "sub_01jhqxaekxx4jzh6jp0by2m3kf"
        ]
    ];
    $rawJsonBody='{"event_id":"evt_01jhqxagvbe182n554m0abxtpk","event_type":"transaction.completed","occurred_at":"2025-01-16T15:57:14.987840Z","notification_id":"ntf_01jhqxah0avqch33fgf404tt6p","data":{"id":"txn_01jhqx6wwbna235d0mc83sta2r","items":[{"price":{"id":"pri_01jhqsmk8f3z654fgsj7j20a5d","name":"Pago Laravel For Beginners","type":"standard","status":"active","quantity":{"maximum":10000,"minimum":1},"tax_mode":"account_setting","created_at":"2025-01-16T14:52:50.831552Z","product_id":"pro_01jhqsgcmrfcvr2bvnjay9enqp","unit_price":{"amount":"1500","currency_code":"USD"},"updated_at":"2025-01-16T14:52:50.831552Z","custom_data":null,"description":"Pago unico","trial_period":null,"billing_cycle":{"interval":"month","frequency":1},"unit_price_overrides":[]},"price_id":"pri_01jhqsmk8f3z654fgsj7j20a5d","quantity":1,"proration":null}],"origin":"web","status":"completed","details":{"totals":{"fee":"125","tax":"260","total":"1500","credit":"0","balance":"0","discount":"0","earnings":"1115","subtotal":"1240","grand_total":"1500","currency_code":"USD","credit_to_balance":"0"},"line_items":[{"id":"txnitm_01jhqx7vmvjstrn84kn2p62mh5","totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"item_id":null,"product":{"id":"pro_01jhqsgcmrfcvr2bvnjay9enqp","name":"Laravel For Beginners","type":"standard","status":"active","image_url":null,"created_at":"2025-01-16T14:50:32.984Z","updated_at":"2025-01-16T14:50:32.984Z","custom_data":{"Product":"One"},"description":"Laravel For Beginners","tax_category":"standard"},"price_id":"pri_01jhqsmk8f3z654fgsj7j20a5d","quantity":1,"tax_rate":"0.21","unit_totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"is_tax_exempt":false,"revised_tax_exempted":false}],"payout_totals":{"fee":"125","tax":"260","total":"1500","credit":"0","balance":"0","discount":"0","earnings":"1115","fee_rate":"0.05","subtotal":"1240","grand_total":"1500","currency_code":"USD","exchange_rate":"1","credit_to_balance":"0"},"tax_rates_used":[{"totals":{"tax":"260","total":"1500","discount":"0","subtotal":"1240"},"tax_rate":"0.21"}],"adjusted_totals":{"fee":"125","tax":"260","total":"1500","earnings":"1115","subtotal":"1240","grand_total":"1500","currency_code":"USD"}},"checkout":{"url":"https:\/\/localhost?_ptxn=txn_01jhqx6wwbna235d0mc83sta2r"},"payments":[{"amount":"1500","status":"captured","created_at":"2025-01-16T15:57:09.77558Z","error_code":null,"captured_at":"2025-01-16T15:57:12.328878Z","method_details":{"card":{"type":"visa","last4":"4242","expiry_year":2025,"expiry_month":5,"cardholder_name":"El Vergon"},"type":"card"},"payment_method_id":"paymtd_01jhqxabqy522yxh0dfg9ta2m4","payment_attempt_id":"b869f115-69e5-46f0-bb53-86a5ba55a0de","stored_payment_method_id":"7e50cc9c-4c76-4c99-a86c-06a8af3f931d"}],"billed_at":"2025-01-16T15:57:12.485064Z","address_id":"add_01jhqx7vbp0pk2jrb48j2tdxbd","created_at":"2025-01-16T15:55:16.285664Z","invoice_id":"inv_01jhqxaep3506h3sq3g6bkaez5","updated_at":"2025-01-16T15:57:14.636836657Z","business_id":null,"custom_data":null,"customer_id":"ctm_01jhqx7vay44920c96e13931wn","discount_id":null,"receipt_data":null,"currency_code":"USD","billing_period":{"ends_at":"2025-02-16T15:57:12.328878Z","starts_at":"2025-01-16T15:57:12.328878Z"},"invoice_number":"10630-10001","billing_details":null,"collection_mode":"automatic","subscription_id":"sub_01jhqxaekxx4jzh6jp0by2m3kf"}}';
    return [$parsedData, $sigHeader, $rawJsonBody];
}

function generateValidSignedPaddleWebhookRequest(array $data, ?int $timestamp = null): array
{
    $ts = $timestamp ?? Carbon::now()->unix();
    $secret = config('services.paddle.notification-endpoint-secret-key');
    $rawJsonBody = json_encode($data);
    $calculatedSig = hash_hmac('sha256', "{$ts}:{$rawJsonBody}", $secret);
    $header = [
        'Paddle-Signature' => "ts={$ts};h1={$calculatedSig}",
    ];
    return [$data, $header];
}
