<?php
/**
 * Debug Script - Check if everything is configured correctly
 * Visit this file in your browser to see configuration status
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug - Telegram Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #ddd; }
        .test-btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 5px; }
        .test-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug - Telegram Configuration Check</h1>

        <?php
        $issues = [];
        $warnings = [];

        // Check PHP version
        echo "<h2>1. PHP Configuration</h2>";
        echo "<div class='info'>";
        echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
        echo "<strong>cURL Extension:</strong> " . (function_exists('curl_init') ? '✅ Enabled' : '❌ Not enabled') . "<br>";
        if (!function_exists('curl_init')) {
            $issues[] = "cURL extension is not enabled. Contact your hosting provider.";
        }
        echo "</div>";

        // Check config file
        echo "<h2>2. Configuration File</h2>";
        $configPath = __DIR__ . '/config.php';
        if (file_exists($configPath)) {
            echo "<div class='success'>✅ Config file exists: " . $configPath . "</div>";
            require_once $configPath;
        } else {
            echo "<div class='error'>❌ Config file NOT found: " . $configPath . "</div>";
            $issues[] = "Config file not found";
        }

        // Check Telegram credentials
        echo "<h2>3. Telegram Credentials</h2>";
        if (defined('TELEGRAM_BOT_TOKEN')) {
            $token = TELEGRAM_BOT_TOKEN;
            if ($token === 'YOUR_BOT_TOKEN_HERE' || empty($token)) {
                echo "<div class='error'>❌ Bot Token not configured</div>";
                $issues[] = "Bot Token not set";
            } else {
                echo "<div class='success'>✅ Bot Token: " . substr($token, 0, 20) . "... (configured)</div>";
            }
        } else {
            echo "<div class='error'>❌ Bot Token constant not defined</div>";
            $issues[] = "Bot Token constant not defined";
        }

        if (defined('TELEGRAM_CHAT_ID')) {
            $chatId = TELEGRAM_CHAT_ID;
            if ($chatId === 'YOUR_CHAT_ID_HERE' || empty($chatId)) {
                echo "<div class='error'>❌ Chat ID not configured</div>";
                $issues[] = "Chat ID not set";
            } else {
                echo "<div class='success'>✅ Chat ID: " . $chatId . "</div>";
            }
        } else {
            echo "<div class='error'>❌ Chat ID constant not defined</div>";
            $issues[] = "Chat ID constant not defined";
        }

        // Test Telegram API
        echo "<h2>4. Telegram API Test</h2>";
        if (defined('TELEGRAM_BOT_TOKEN') && defined('TELEGRAM_CHAT_ID') && 
            TELEGRAM_BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE' && TELEGRAM_CHAT_ID !== 'YOUR_CHAT_ID_HERE') {
            
            $testMessage = "🧪 *Debug Test Message*\n\n";
            $testMessage .= "If you see this, your Telegram integration is working! ✅\n";
            $testMessage .= "Time: " . date('Y-m-d H:i:s');

            $telegramData = [
                'chat_id' => TELEGRAM_CHAT_ID,
                'text' => $testMessage,
                'parse_mode' => 'Markdown'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($telegramData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                if (isset($result['ok']) && $result['ok'] === true) {
                    echo "<div class='success'>✅ Telegram API Test: SUCCESS! Check your Telegram for the test message.</div>";
                } else {
                    echo "<div class='error'>❌ Telegram API Error: " . ($result['description'] ?? 'Unknown error') . "</div>";
                    echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
                    $issues[] = "Telegram API returned error: " . ($result['description'] ?? 'Unknown');
                }
            } else {
                echo "<div class='error'>❌ HTTP Error: " . $httpCode . "</div>";
                if ($curlError) {
                    echo "<div class='error'>cURL Error: " . $curlError . "</div>";
                }
                echo "<pre>" . htmlspecialchars($response) . "</pre>";
                $issues[] = "HTTP Error " . $httpCode;
            }
        } else {
            echo "<div class='warning'>⚠️ Cannot test Telegram API - credentials not configured</div>";
        }

        // Check file permissions
        echo "<h2>5. File Permissions</h2>";
        $files = ['config.php', 'submit.php'];
        foreach ($files as $file) {
            $filePath = __DIR__ . '/' . $file;
            if (file_exists($filePath)) {
                $perms = substr(sprintf('%o', fileperms($filePath)), -4);
                echo "<div class='info'><strong>" . $file . ":</strong> " . $perms . "</div>";
            }
        }

        // Check submit.php path
        echo "<h2>6. File Paths</h2>";
        echo "<div class='info'>";
        echo "<strong>Current Directory:</strong> " . __DIR__ . "<br>";
        echo "<strong>Config Path:</strong> " . $configPath . "<br>";
        echo "<strong>Submit.php Path:</strong> " . __DIR__ . '/submit.php' . "<br>";
        echo "<strong>Config Exists:</strong> " . (file_exists($configPath) ? 'Yes' : 'No') . "<br>";
        echo "<strong>Submit.php Exists:</strong> " . (file_exists(__DIR__ . '/submit.php') ? 'Yes' : 'No') . "<br>";
        echo "</div>";

        // Summary
        echo "<h2>7. Summary</h2>";
        if (empty($issues)) {
            echo "<div class='success'><strong>✅ All checks passed! Your configuration looks good.</strong></div>";
            echo "<div class='info'>If you're still not receiving messages, check:<br>";
            echo "1. Browser console (F12) for JavaScript errors<br>";
            echo "2. Server error logs in cPanel<br>";
            echo "3. Make sure you haven't blocked your Telegram bot<br>";
            echo "4. Try the test_form.html page</div>";
        } else {
            echo "<div class='error'><strong>❌ Issues Found:</strong></div>";
            echo "<ul>";
            foreach ($issues as $issue) {
                echo "<li>" . $issue . "</li>";
            }
            echo "</ul>";
        }
        ?>

        <hr>
        <h2>Quick Test</h2>
        <button class="test-btn" onclick="window.location.href='test_form.html'">Test Form Submission</button>
        <button class="test-btn" onclick="window.location.href='test_telegram.php'">Test Telegram PHP</button>
        <button class="test-btn" onclick="window.location.href='wallet/index.html'">Go to Wallet Page</button>

        <div class="warning" style="margin-top: 20px;">
            <strong>⚠️ Security Note:</strong> Delete this debug.php file after testing for security reasons.
        </div>
    </div>
</body>
</html>

