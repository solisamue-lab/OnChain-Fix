<?php
/**
 * Setup Verification Script
 * Run this after uploading to cPanel to verify everything is configured correctly
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .check { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .check-item { margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 3px; }
        .status { font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Setup Verification</h1>
        <p>This script checks if your deployment is configured correctly.</p>
        
        <?php
        $checks = [];
        $errors = 0;
        $warnings = 0;
        
        // Check 1: PHP Version
        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '5.6.0', '>=');
        $checks[] = [
            'name' => 'PHP Version',
            'status' => $phpOk ? 'success' : 'error',
            'message' => $phpOk ? "PHP $phpVersion (OK)" : "PHP $phpVersion (Requires 5.6+)",
            'details' => ''
        ];
        if (!$phpOk) $errors++;
        
        // Check 2: Config file exists
        $configExists = file_exists(__DIR__ . '/config.php');
        $checks[] = [
            'name' => 'Config File',
            'status' => $configExists ? 'success' : 'error',
            'message' => $configExists ? 'config.php found' : 'config.php NOT FOUND',
            'details' => $configExists ? '' : 'Make sure config.php is uploaded to the root directory'
        ];
        if (!$configExists) $errors++;
        
        // Check 3: Submit file exists
        $submitExists = file_exists(__DIR__ . '/submit.php');
        $checks[] = [
            'name' => 'Submit Handler',
            'status' => $submitExists ? 'success' : 'error',
            'message' => $submitExists ? 'submit.php found' : 'submit.php NOT FOUND',
            'details' => $submitExists ? '' : 'Make sure submit.php is uploaded to the root directory'
        ];
        if (!$submitExists) $errors++;
        
        // Check 4: Wallet folder exists
        $walletExists = is_dir(__DIR__ . '/wallet');
        $checks[] = [
            'name' => 'Wallet Folder',
            'status' => $walletExists ? 'success' : 'error',
            'message' => $walletExists ? 'wallet/ folder found' : 'wallet/ folder NOT FOUND',
            'details' => $walletExists ? '' : 'Make sure the wallet folder is uploaded'
        ];
        if (!$walletExists) $errors++;
        
        // Check 5: Telegram Configuration
        if ($configExists) {
            require_once __DIR__ . '/config.php';
            $tokenSet = defined('TELEGRAM_BOT_TOKEN') && TELEGRAM_BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE' && !empty(TELEGRAM_BOT_TOKEN);
            $chatIdSet = defined('TELEGRAM_CHAT_ID') && TELEGRAM_CHAT_ID !== 'YOUR_CHAT_ID_HERE' && !empty(TELEGRAM_CHAT_ID);
            
            $checks[] = [
                'name' => 'Telegram Bot Token',
                'status' => $tokenSet ? 'success' : 'error',
                'message' => $tokenSet ? 'Bot Token configured' : 'Bot Token NOT configured',
                'details' => $tokenSet ? 'Token: ' . substr(TELEGRAM_BOT_TOKEN, 0, 10) . '...' : 'Edit config.php and add your bot token'
            ];
            if (!$tokenSet) $errors++;
            
            $checks[] = [
                'name' => 'Telegram Chat ID',
                'status' => $chatIdSet ? 'success' : 'error',
                'message' => $chatIdSet ? 'Chat ID configured' : 'Chat ID NOT configured',
                'details' => $chatIdSet ? 'Chat ID: ' . TELEGRAM_CHAT_ID : 'Edit config.php and add your chat ID'
            ];
            if (!$chatIdSet) $errors++;
        }
        
        // Check 6: File Permissions
        if ($configExists) {
            $configPerms = substr(sprintf('%o', fileperms(__DIR__ . '/config.php')), -4);
            $checks[] = [
                'name' => 'Config File Permissions',
                'status' => 'info',
                'message' => "Permissions: $configPerms",
                'details' => 'Recommended: 644 (readable by web server)'
            ];
        }
        
        // Check 7: .htaccess file
        $htaccessExists = file_exists(__DIR__ . '/.htaccess');
        $checks[] = [
            'name' => '.htaccess File',
            'status' => $htaccessExists ? 'success' : 'warning',
            'message' => $htaccessExists ? '.htaccess found' : '.htaccess NOT FOUND (optional)',
            'details' => $htaccessExists ? '' : 'Recommended for better security and routing'
        ];
        if (!$htaccessExists) $warnings++;
        
        // Check 8: Test Telegram Connection
        if ($configExists && isset($tokenSet) && $tokenSet && isset($chatIdSet) && $chatIdSet) {
            $testUrl = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/getMe';
            $ch = curl_init($testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $telegramOk = ($httpCode === 200 && !empty($response));
            $checks[] = [
                'name' => 'Telegram API Connection',
                'status' => $telegramOk ? 'success' : 'error',
                'message' => $telegramOk ? 'Can connect to Telegram API' : 'Cannot connect to Telegram API',
                'details' => $telegramOk ? 'Your bot is reachable' : 'Check your bot token or server internet connection'
            ];
            if (!$telegramOk) $errors++;
        }
        
        // Display results
        foreach ($checks as $check) {
            echo '<div class="check ' . $check['status'] . '">';
            echo '<div class="check-item">';
            echo '<span class="status">' . ($check['status'] === 'success' ? '✅' : ($check['status'] === 'error' ? '❌' : '⚠️')) . ' ' . $check['name'] . ':</span> ';
            echo $check['message'];
            if (!empty($check['details'])) {
                echo '<br><small>' . $check['details'] . '</small>';
            }
            echo '</div></div>';
        }
        
        // Summary
        echo '<div class="check ' . ($errors === 0 ? 'success' : 'error') . '" style="margin-top: 30px; padding: 20px;">';
        echo '<h2>📊 Summary</h2>';
        echo '<p><strong>Errors:</strong> ' . $errors . '</p>';
        echo '<p><strong>Warnings:</strong> ' . $warnings . '</p>';
        
        if ($errors === 0) {
            echo '<p style="font-size: 18px; margin-top: 15px;"><strong>✅ All critical checks passed! Your setup looks good.</strong></p>';
            echo '<p>Next steps:</p>';
            echo '<ul>';
            echo '<li>Test your homepage: <code>https://' . $_SERVER['HTTP_HOST'] . '/</code></li>';
            echo '<li>Test wallet page: <code>https://' . $_SERVER['HTTP_HOST'] . '/wallet/</code></li>';
            echo '<li>Test Telegram: <code>https://' . $_SERVER['HTTP_HOST'] . '/test_telegram.php</code></li>';
            echo '</ul>';
        } else {
            echo '<p style="font-size: 18px; margin-top: 15px;"><strong>❌ Please fix the errors above before deploying.</strong></p>';
        }
        echo '</div>';
        ?>
        
        <div class="check info" style="margin-top: 20px;">
            <p><strong>ℹ️ Note:</strong> Delete this file (<code>verify_setup.php</code>) after verification for security.</p>
        </div>
    </div>
</body>
</html>

