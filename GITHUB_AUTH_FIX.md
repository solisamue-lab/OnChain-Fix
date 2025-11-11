# 🔐 GitHub Authentication Fix

## Issue
Getting permission denied when pushing to GitHub.

## Solutions

### Option 1: Use Personal Access Token (Recommended)

1. **Create Personal Access Token:**
   - Go to: https://github.com/settings/tokens
   - Click "Generate new token" → "Generate new token (classic)"
   - Name it: "Vercel Deployment"
   - Select scopes: ✅ `repo` (full control)
   - Click "Generate token"
   - **COPY THE TOKEN** (you won't see it again!)

2. **Push using token:**
   ```bash
   cd /Users/olaitansoyoye/Downloads/testos
   git push https://YOUR_TOKEN@github.com/solisamue-lab/OnChain-Fix.git main
   ```
   Replace `YOUR_TOKEN` with the token you copied.

### Option 2: Use SSH (If you have SSH key set up)

1. **Change remote to SSH:**
   ```bash
   git remote set-url origin git@github.com:solisamue-lab/OnChain-Fix.git
   git push -u origin main
   ```

### Option 3: Use GitHub CLI

1. **Install GitHub CLI** (if not installed):
   ```bash
   brew install gh
   ```

2. **Authenticate:**
   ```bash
   gh auth login
   ```

3. **Push:**
   ```bash
   git push -u origin main
   ```

### Option 4: Use GitHub Desktop

1. Download GitHub Desktop: https://desktop.github.com/
2. Login with your GitHub account
3. Add the repository
4. Push from the app

---

## Quick Fix (Easiest)

**Use Personal Access Token:**

1. Get token from: https://github.com/settings/tokens
2. Run this command (replace YOUR_TOKEN):
   ```bash
   cd /Users/olaitansoyoye/Downloads/testos
   git push https://YOUR_TOKEN@github.com/solisamue-lab/OnChain-Fix.git main
   ```

---

**After successful push, Vercel will auto-deploy!** 🚀

