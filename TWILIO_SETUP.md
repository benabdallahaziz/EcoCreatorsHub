# Twilio SMS Setup Guide

## Step 1: Create Twilio Account
1. Go to [Twilio Console](https://console.twilio.com/)
2. Sign up for free account
3. Get $15 free credit for testing

## Step 2: Get Credentials
1. From Twilio Console Dashboard, copy:
   - Account SID
   - Auth Token
2. Go to Phone Numbers → Manage → Active numbers
3. Copy your Twilio phone number

## Step 3: Configure Application
Update `.env` file with your Twilio credentials:
```
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_auth_token_here
TWILIO_PHONE=+1234567890
ADMIN_PHONE=+21621043481
```

## Step 4: Test Moderation
1. Clear cache: `php bin/console cache:clear`
2. Create a journal with title or description containing "kalma khayba"
3. Admin phone (+21621043481) should receive SMS alert

## Features
- ✅ Automatic bad word detection
- ✅ SMS alerts to admin phone
- ✅ Artist name and journal title in alert
- ✅ Error handling (won't break if Twilio fails)

## SMS Alert Format
```
🚨 ALERTE MODÉRATION

Artiste: [Artist Name]
Journal: [Journal Title]
Mot détecté: kalma khayba

Vérifiez le contenu sur EcoCreatorsHub
```

## Cost
Twilio charges per SMS sent. Check current pricing at:
https://www.twilio.com/pricing