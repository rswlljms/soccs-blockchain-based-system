# Public Access Setup Guide

This guide explains how to expose your local SOCCS Financial Management system to the internet with a public URL while maintaining blockchain functionality.

## Overview

Your application consists of two services:
1. **PHP Web Server** (XAMPP) - Main application on port 80/443
2. **Blockchain Signer Service** (Node.js) - Blockchain API on port 3001

Both services need to be accessible via public URLs for the system to work properly.

## Prerequisites

- Node.js installed and blockchain signer service running
- XAMPP/Apache running
- ngrok account (free tier available) or alternative tunneling service

## Method 1: Using ngrok (Recommended)

### Step 1: Install ngrok

Download from [ngrok.com](https://ngrok.com/download) or install via package manager:

**Windows (PowerShell):**
```powershell
# Using Chocolatey
choco install ngrok

# Or download from https://ngrok.com/download
```

**Linux/Mac:**
```bash
# Using Homebrew (Mac)
brew install ngrok

# Or download from https://ngrok.com/download
```

### Step 2: Sign up and Get Auth Token

1. Create a free account at [ngrok.com](https://dashboard.ngrok.com/signup)
2. Get your authtoken from the dashboard
3. Configure ngrok:
```bash
ngrok config add-authtoken YOUR_AUTH_TOKEN
```

### Step 3: Expose Blockchain Service (Port 3001)

Open a terminal and run:
```bash
ngrok http 3001
```

This will give you a public URL like: `https://abc123.ngrok-free.app`

**Note the HTTPS URL** - you'll need this for configuration.

### Step 4: Expose PHP Web Server (Port 80)

Open another terminal and run:
```bash
ngrok http 80
```

This will give you another public URL like: `https://xyz789.ngrok-free.app`

### Step 5: Configure Application

#### Option A: Environment Variable (Recommended)

Set the `BLOCKCHAIN_URL` environment variable before starting your services:

**Windows (PowerShell):**
```powershell
$env:BLOCKCHAIN_URL = "https://abc123.ngrok-free.app"
```

**Windows (Command Prompt):**
```cmd
set BLOCKCHAIN_URL=https://abc123.ngrok-free.app
```

**Linux/Mac:**
```bash
export BLOCKCHAIN_URL="https://abc123.ngrok-free.app"
```

#### Option B: Update app_config.php

Edit `includes/app_config.php` and update the default value:

```php
'BLOCKCHAIN_URL' => 'https://abc123.ngrok-free.app',
```

### Step 6: Update Blockchain Signer Service

The blockchain signer service is already configured to accept external connections (listens on `0.0.0.0`).

### Step 7: Access Your Application

1. Access your PHP application via the ngrok URL for port 80: `https://xyz789.ngrok-free.app`
2. The blockchain service will automatically use the configured URL

## Method 2: Using Cloudflare Tunnel (Alternative)

Cloudflare Tunnel provides a free alternative with custom domains.

### Installation

```bash
# Download cloudflared from https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/
```

### Setup

1. Authenticate:
```bash
cloudflared tunnel login
```

2. Create tunnel:
```bash
cloudflared tunnel create soccs-tunnel
```

3. Configure tunnel (create `config.yml`):
```yaml
tunnel: soccs-tunnel
credentials-file: C:\Users\YourUser\.cloudflared\UUID.json

ingress:
  - hostname: your-app.yourdomain.com
    service: http://localhost:80
  - hostname: blockchain.yourdomain.com
    service: http://localhost:3001
  - service: http_status:404
```

4. Run tunnel:
```bash
cloudflared tunnel run soccs-tunnel
```

## Method 3: Using localtunnel (Quick Alternative)

### Installation

```bash
npm install -g localtunnel
```

### Usage

**For Blockchain Service:**
```bash
lt --port 3001
```

**For Web Server:**
```bash
lt --port 80
```

Update `BLOCKCHAIN_URL` with the provided URL.

## Important Notes

### Security Considerations

1. **HTTPS Only**: Always use HTTPS URLs for blockchain service in production
2. **Authentication**: Ensure your application has proper authentication enabled
3. **Firewall**: Consider restricting access to specific IPs if possible
4. **Environment Variables**: Never commit sensitive URLs or tokens to version control

### ngrok Free Tier Limitations

- URLs change on each restart (unless using paid plan)
- Connection limits apply
- Bandwidth limits may apply

### Persistent URLs (ngrok Paid Plans)

For production use, consider:
- ngrok paid plan with static domains
- Cloudflare Tunnel with custom domain
- VPS deployment with proper domain setup

## Testing

1. Start both services:
   - XAMPP/Apache
   - Blockchain signer: `cd blockchain/blockchain-signer && npm start`

2. Start ngrok tunnels for both ports

3. Set `BLOCKCHAIN_URL` environment variable

4. Access your application via the public URL

5. Test blockchain functionality:
   - Add a fund/expense
   - Verify transaction appears on blockchain
   - Check transaction hash is generated

## Troubleshooting

### Blockchain Service Not Accessible

1. Verify blockchain service is running: `http://localhost:3001`
2. Check ngrok tunnel is active
3. Verify `BLOCKCHAIN_URL` is set correctly
4. Check firewall isn't blocking port 3001

### CORS Errors

The blockchain signer already has CORS enabled. If you see CORS errors:
1. Verify the blockchain URL in config matches ngrok URL
2. Check ngrok tunnel is using HTTPS
3. Clear browser cache

### Connection Timeout

1. Verify both ngrok tunnels are running
2. Check service ports are correct (80 and 3001)
3. Verify environment variable is set
4. Restart services after setting environment variable

## Production Deployment

For production, consider:
1. Deploying to a VPS or cloud provider
2. Using a proper domain name with SSL
3. Setting up reverse proxy (nginx/Apache)
4. Using environment-specific configuration files
5. Implementing proper monitoring and logging

## Quick Start Script

Create a `start-public.bat` (Windows) or `start-public.sh` (Linux/Mac) script:

**Windows (start-public.bat):**
```batch
@echo off
echo Starting SOCCS Financial Management with public access...

REM Set blockchain URL (update with your ngrok URL)
set BLOCKCHAIN_URL=https://your-ngrok-url.ngrok-free.app

REM Start blockchain service
start "Blockchain Service" cmd /k "cd blockchain\blockchain-signer && npm start"

REM Start ngrok for blockchain
start "ngrok Blockchain" cmd /k "ngrok http 3001"

REM Start ngrok for web server
start "ngrok Web Server" cmd /k "ngrok http 80"

echo.
echo Services starting...
echo Update BLOCKCHAIN_URL in app_config.php or set as environment variable
echo Access your application via the ngrok URL for port 80
pause
```

**Linux/Mac (start-public.sh):**
```bash
#!/bin/bash
echo "Starting SOCCS Financial Management with public access..."

# Set blockchain URL (update with your ngrok URL)
export BLOCKCHAIN_URL="https://your-ngrok-url.ngrok-free.app"

# Start blockchain service
cd blockchain/blockchain-signer && npm start &
BLOCKCHAIN_PID=$!

# Start ngrok for blockchain
ngrok http 3001 &
NGROK_BLOCKCHAIN_PID=$!

# Start ngrok for web server
ngrok http 80 &
NGROK_WEB_PID=$!

echo ""
echo "Services starting..."
echo "Update BLOCKCHAIN_URL in app_config.php or set as environment variable"
echo "Access your application via the ngrok URL for port 80"
echo ""
echo "Press Ctrl+C to stop all services"

# Wait for interrupt
trap "kill $BLOCKCHAIN_PID $NGROK_BLOCKCHAIN_PID $NGROK_WEB_PID; exit" INT
wait
```

## Support

For issues or questions:
1. Check ngrok/cloudflared documentation
2. Verify all services are running
3. Check application logs
4. Review blockchain service console output

