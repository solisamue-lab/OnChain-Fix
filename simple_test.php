<?php
/**
 * SIMPLE DIRECT TEST - Just tests Telegram connection
 * Visit: https://yourdomain.com/simple_test.php
 */

require_once 'config.php';

// Simple test message
$message = "TEST MESSAGE - " . date('Y-m-d H:i:s');

$url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
$data = [
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: text/plain');
echo "HTTP Code: " . $httpCode . "\n\n";
echo "Response:\n" . $result . "\n\n";

$json = json_decode($result, true);
if (isset($json['ok']) && $json['ok']) {
    echo "✅ SUCCESS! Check your Telegram!\n";
} else {
    echo "❌ FAILED!\n";
    echo "Error: " . ($json['description'] ?? 'Unknown') . "\n";
}
?>

