# Google Translate API Setup Guide

## Step 1: Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the "Cloud Translation API"

## Step 2: Create API Key
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the generated API key

## Step 3: Configure Application
1. Open `.env` file
2. Replace `your_api_key_here` with your actual API key:
   ```
   GOOGLE_TRANSLATE_API_KEY=AIzaSyC-your-actual-api-key-here
   ```

## Step 4: Test Translation
1. Clear cache: `php bin/console cache:clear`
2. Visit any journal page
3. Click "Traduire" dropdown
4. Select a language to test server-side translation

## Features
- ✅ Server-side translation using Google Cloud Translate API
- ✅ Translates journal titles, descriptions, and all steps
- ✅ Real-time translation without page reload
- ✅ Error handling and loading states
- ✅ Support for 5+ languages

## Supported Languages
- 🇺🇸 English
- 🇪🇸 Español  
- 🇩🇪 Deutsch
- 🇮🇹 Italiano
- 🇸🇦 العربية

## Cost
Google Translate API charges per character translated. Check current pricing at:
https://cloud.google.com/translate/pricing