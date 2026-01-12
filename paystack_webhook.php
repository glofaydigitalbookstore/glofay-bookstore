<?php
// paystack_webhook.php

// Paystack secret (LIVE)
$paystack_secret = "sk_live_YOUR_SECRET_KEY";

// Read the request body
$input = @file_get_contents("php://input");
$event = json_decode($input, true);

// Verify the signature (recommended)
$signature = isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) ? $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] : '';
$computed_hash = hash_hmac('sha512', $input, $paystack_secret);

if ($signature !== $computed_hash) {
    http_response_code(400);
    exit("Invalid signature");
}

// Only respond to successful charges
if ($event['event'] === 'charge.success') {
    $reference = $event['data']['reference'];
    $email = $event['data']['customer']['email'] ?? '';
    $amount = $event['data']['amount'] / 100;
    $metadata = $event['data']['metadata'] ?? [];

    // TODO: Save to database or log file
    // Example:
    // file_put_contents("payments_log.csv", "$reference,$email,$amount\n", FILE_APPEND);
}

http_response_code(200);
echo json_encode(["status" => "success"]);