#!/bin/bash

# Script to push code to GitHub and deploy to Vercel

echo "🚀 Pushing to GitHub..."

# Check if remote is set
if git remote get-url origin &>/dev/null; then
    echo "✅ Remote already configured"
    git remote -v
else
    echo "❌ No remote configured"
    echo ""
    echo "Please provide your GitHub repository URL:"
    echo "Example: https://github.com/username/repo-name.git"
    read -p "Repository URL: " REPO_URL
    
    if [ -z "$REPO_URL" ]; then
        echo "❌ No URL provided. Exiting."
        exit 1
    fi
    
    git remote add origin "$REPO_URL"
    echo "✅ Remote added: $REPO_URL"
fi

echo ""
echo "📤 Pushing to GitHub..."
git push -u origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Successfully pushed to GitHub!"
    echo ""
    echo "📋 Next steps:"
    echo "1. Go to https://vercel.com"
    echo "2. Click 'Add New Project'"
    echo "3. Select your repository"
    echo "4. Add environment variables:"
    echo "   - TELEGRAM_BOT_TOKEN = 8207229530:AAF2CAILD5IU8etTbdPfp5MWQbrd2bgYux0"
    echo "   - TELEGRAM_CHAT_ID = 7811008623"
    echo "5. Click 'Deploy'"
else
    echo ""
    echo "❌ Push failed. Please check:"
    echo "- Repository URL is correct"
    echo "- You have access to the repository"
    echo "- Repository exists on GitHub"
fi

