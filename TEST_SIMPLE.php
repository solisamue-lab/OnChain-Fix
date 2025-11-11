<?php
// SUPER SIMPLE TEST - Just sends a message to Telegram
// Visit: yourdomain.com/TEST_SIMPLE.php

$botToken = '8207229530:AAF2CAILD5IU8etTbdPfp5MWQbrd2bgYux0';
$chatId = '7811008623';

$message = "TEST MESSAGE - " . date('H:i:s');

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$data = [
    'chat_id' => $chatId,
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
echo "HTTP Code: $httpCode\n\n";
echo "Response:\n$result\n\n";

$json = json_decode($result, true);
if ($json && $json['ok']) {
    echo "✅ SUCCESS! Check your Telegram!\n";
} else {
    echo "❌ FAILED!\n";
    echo "Error: " . ($json['description'] ?? 'Unknown') . "\n";
}
?>

