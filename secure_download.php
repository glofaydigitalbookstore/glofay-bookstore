<?php
if (!isset($_GET['file'], $_GET['ref'], $_GET['email'])) {
    die("Invalid request.");
}

$filename = basename($_GET['file']); // sanitize filename
$reference = $_GET['ref'];
$email = $_GET['email'];

// Full path to the PDF
$filepath = __DIR__ . "/" . $filename;

// Check file exists
if (!file_exists($filepath)) {
    die("File not found.");
}

// Paystack secret key (LIVE)
$secret_key = "sk_live_YOUR_SECRET_KEY"; // replace with your live secret key

// Verify transaction
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . urlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key",
]);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    die("Unable to verify payment.");
}

$result = json_decode($response, true);

// Check if payment was successful and email matches
if ($result['status'] && $result['data']['status'] === 'success' && $result['data']['customer']['email'] === $email) {
    // Force download
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
    header("Content-Length: " . filesize($filepath));
    header("Cache-Control: no-cache");
    readfile($filepath);
    exit;
} else {
    die("Payment verification failed or email does not match.");
}
