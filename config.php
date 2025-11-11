<?php
/**
 * Telegram Bot Configuration
 * 
 * INSTRUCTIONS:
 * 1. Get your Telegram Bot Token from @BotFather on Telegram
 * 2. Get your Telegram Chat ID by messaging @userinfobot on Telegram
 * 3. Replace the values below with your actual credentials
 */

// Telegram Bot Token (from @BotFather)
define('TELEGRAM_BOT_TOKEN', '8207229530:AAF2CAILD5IU8etTbdPfp5MWQbrd2bgYux0');

// Your Telegram Chat ID (from @userinfobot)
define('TELEGRAM_CHAT_ID', '7811008623');

// Telegram API URL
define('TELEGRAM_API_URL', 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage');

?>

