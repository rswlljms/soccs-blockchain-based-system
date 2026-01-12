# Environment Configuration Guide

## Overview

SOCCSChain uses environment variables to store sensitive configuration data like API keys, passwords, and service URLs. This keeps credentials secure and out of version control.

## Setup Instructions

### 1. Create Environment File

Create a `.env` file in the project root directory:

```bash
# Copy from example (if available) or create new
cp .env.example .env
```

### 2. Configure Environment Variables

Add the following variables to your `.env` file:

```env
# OCR Space API Key (for document verification)
OCR_SPACE_API_KEY=your_ocr_space_api_key_here

# SMTP Configuration (Gmail)
smtp_username=your_email@gmail.com
smtp_password=your_gmail_app_password_here

# Blockchain Service URL
BLOCKCHAIN_URL=http://localhost:3001
```

### 3. Get API Keys

#### OCR Space API Key
1. Visit https://ocr.space/ocrapi
2. Sign up for a free account
3. Get your API key from the dashboard
4. Add it to your `.env` file

#### Gmail App Password
1. Go to Google Account Settings
2. Navigate to Security > 2-Step Verification
3. Scroll down to "App passwords"
4. Generate a new app password for "Mail"
5. Copy the 16-character password
6. Add it to your `.env` file

### 4. Blockchain Signer Configuration

For blockchain features, create a `.env` file in `blockchain/blockchain-signer/`:

```env
CONTRACT_ADDRESS=your_deployed_contract_address
INFURA_URL=https://sepolia.infura.io/v3/your_project_id
PRIVATE_KEY=your_wallet_private_key
```

**Never commit these files to version control!**

## Security Notes

- The `.env` file is already in `.gitignore`
- Never share your API keys or passwords publicly
- Rotate credentials if they are accidentally exposed
- Use different credentials for development and production

## Verifying Configuration

After setup, verify your configuration works:

1. **Email**: Test registration to verify SMTP settings
2. **OCR**: Upload a document to test OCR functionality
3. **Blockchain**: Check blockchain signer logs for connection status

## Troubleshooting

### Email not sending
- Verify Gmail App Password is correct (16 characters, no spaces)
- Ensure "Less secure app access" is not needed (use App Passwords instead)
- Check spam folder for test emails

### OCR not working
- Verify API key is valid at https://ocr.space/ocrapi
- Check API usage limits (free tier has daily limits)

### Blockchain connection failed
- Ensure blockchain-signer service is running
- Verify INFURA_URL or RPC endpoint is correct
- Check contract address matches deployed contract

