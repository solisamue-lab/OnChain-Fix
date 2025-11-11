<?php
/**
 * SEND TEST MESSAGE NOW
 * Upload this file and visit it in your browser
 * It will send a test message to your Telegram immediately
 */

// Your Telegram credentials
$botToken = '8207229530:AAF2CAILD5IU8etTbdPfp5MWQbrd2bgYux0';
$chatId = '7811008623';

// Test message
$message = "🧪 TEST MESSAGE\n\n";
$message .= "Time: " . date('Y-m-d H:i:s') . "\n";
$message .= "If you see this, your Telegram bot is working! ✅";

// Telegram API URL
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";

// Prepare data
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

// Send to Telegram
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Display result
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Test Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 2px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 2px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
        h1 { color: #333; }
        .big { font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 Telegram Test Message</h1>
        
        <div class="info">
            <strong>Configuration:</strong><br>
            Bot Token: <?php echo substr($botToken, 0, 20); ?>...<br>
            Chat ID: <?php echo $chatId; ?><br>
            HTTP Code: <?php echo $httpCode; ?>
        </div>

        <?php
        $result = json_decode($response, true);
        
        if ($httpCode == 200 && isset($result['ok']) && $result['ok'] === true) {
            echo '<div class="success">';
            echo '<div class="big">✅ SUCCESS!</div>';
            echo '<p><strong>Message sent successfully to your Telegram!</strong></p>';
            echo '<p>Check your Telegram now - you should see the test message.</p>';
            echo '<p>Message ID: ' . ($result['result']['message_id'] ?? 'N/A') . '</p>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>✅ Your Telegram integration is working!</strong><br>';
            echo 'Now test the form on your website - it should work too.';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<div class="big">❌ FAILED</div>';
            
            if ($curlError) {
                echo '<p><strong>Connection Error:</strong> ' . htmlspecialchars($curlError) . '</p>';
                echo '<p>Your server cannot connect to Telegram API.</p>';
                echo '<p><strong>Solution:</strong> Contact your hosting provider to enable outbound HTTPS connections.</p>';
            } elseif (isset($result['description'])) {
                $errorDesc = $result['description'];
                echo '<p><strong>Error:</strong> ' . htmlspecialchars($errorDesc) . '</p>';
                
                if (strpos($errorDesc, 'chat not found') !== false || strpos($errorDesc, 'chat_id') !== false) {
                    echo '<p><strong>SOLUTION:</strong></p>';
                    echo '<ol>';
                    echo '<li>Go to Telegram and message @userinfobot</li>';
                    echo '<li>Copy your Chat ID</li>';
                    echo '<li>Make sure you\'ve sent /start to your bot first</li>';
                    echo '<li>Update config.php with the correct Chat ID</li>';
                    echo '</ol>';
                } elseif (strpos($errorDesc, 'Unauthorized') !== false) {
                    echo '<p><strong>SOLUTION:</strong></p>';
                    echo '<ol>';
                    echo '<li>Go to Telegram and message @BotFather</li>';
                    echo '<li>Send /mybots</li>';
                    echo '<li>Select your bot</li>';
                    echo '<li>Click "API Token"</li>';
                    echo '<li>Copy the token and update config.php</li>';
                    echo '</ol>';
                }
            } else {
                echo '<p>Unknown error occurred.</p>';
            }
            echo '</div>';
        }
        ?>

        <h3>Raw Response:</h3>
        <pre><?php echo htmlspecialchars($response); ?></pre>

        <hr>
        <h3>Next Steps:</h3>
        <ol>
            <li>If you see "SUCCESS" above, check your Telegram for the test message</li>
            <li>If you see "FAILED", follow the solution steps above</li>
            <li>Once this test works, your website forms will work too</li>
        </ol>
    </div>
</body>
</html>

