# SOCCS System Contract Deployment Guide

## Prerequisites
- MetaMask or another Web3 wallet installed
- Sufficient ETH in your wallet for gas fees
- Access to Remix IDE (https://remix.ethereum.org)
- Your network RPC URL (Infura, Alchemy, or local node)

## Step-by-Step Deployment

### 1. Open Remix IDE
- Navigate to https://remix.ethereum.org
- Wait for the interface to fully load

### 2. Create Contract File
1. In the **File Explorer** (left sidebar), click the `contracts` folder
2. Click the **+** icon to create a new file
3. Name it: `SOCCS_SYSTEM.sol`
4. Copy the entire content from `blockchain/contracts/SOCCS_SYSTEM.sol` in your project
5. Paste it into the Remix editor

### 3. Compile Contract
1. Click the **Solidity Compiler** icon (✓ icon in left sidebar)
2. Select **Compiler version**: `0.8.0` or higher (must match `pragma solidity ^0.8.0`)
3. Click **Compile SOCCS_SYSTEM.sol**
4. Wait for green checkmark - compilation successful
5. If there are errors, check the error messages and fix them

### 4. Get the ABI and Compiler Settings
1. After successful compilation, click **Compilation Details** button
2. In the popup, scroll to find **ABI** section
3. Click the **copy icon** next to the ABI JSON
4. **Save this** - you'll need to update `blockchain/blockchain-signer/abi.json`
5. **Note Compiler Settings** (needed for Etherscan verification):
   - **Compiler Version**: Note the exact version (e.g., `0.8.20`)
   - **Optimization**: Check if enabled (Yes/No)
   - **Runs**: If optimization enabled, note the value (e.g., `200`)
   - **License**: Should be `MIT` (matches contract SPDX identifier)

### 5. Deploy Contract
1. Click **Deploy & Run Transactions** icon (bottom of left sidebar, Ethereum logo)
2. Select **Environment**:
   - **Injected Provider - MetaMask** (if using MetaMask)
   - **Web3 Provider** (if using custom RPC - enter your Infura/Alchemy URL)
3. Select **Account**: Choose the account that will deploy (must have ETH for gas)
4. Select **Contract**: `SOCCS_SYSTEM`
5. Click **Deploy** button
6. **Confirm transaction** in MetaMask/wallet
7. Wait for deployment confirmation (green checkmark)

### 6. Get Contract Address
1. After deployment, find your contract in **Deployed Contracts** section
2. The contract address is shown above the contract name (starts with `0x...`)
3. **Copy this address** - you'll need it for your `.env` file

### 7. Update Project Configuration

#### Update `.env` file
1. Navigate to `blockchain/blockchain-signer/` folder
2. Open or create `.env` file
3. Update or add:
```env
CONTRACT_ADDRESS=0xYourNewContractAddressHere
INFURA_URL=https://your-network.infura.io/v3/YOUR_PROJECT_ID
PRIVATE_KEY=your_private_key_here
```

#### Verify ABI (if needed)
1. Open `blockchain/blockchain-signer/abi.json`
2. Compare with the ABI from Remix
3. If different, replace the entire content with the ABI from Remix

### 8. Verify Contract Source Code on Etherscan

#### Method 1: Using Remix Etherscan Plugin (Recommended)

1. **Install Etherscan Plugin in Remix**:
   - In Remix, click the **Plugin Manager** icon (bottom of left sidebar, puzzle piece icon)
   - Search for **"Etherscan"** or **"flatten"**
   - Click **Activate** on the **Etherscan - Contract Verification** plugin

2. **Get Compiler Settings**:
   - Go to **Solidity Compiler** tab
   - Note the **Compiler version** (e.g., `0.8.20`)
   - Note **Optimization** settings:
     - If enabled, note the **Runs** value (e.g., 200)
     - If disabled, optimization is `false`

3. **Verify Contract**:
   - Go to **Deploy & Run Transactions** tab
   - Find your deployed contract in **Deployed Contracts** section
   - Click the **⋮** (three dots) menu next to your contract
   - Select **Verify on Etherscan** or **Verify on Block Explorer**
   - Fill in the form:
     - **Contract Address**: Your contract address (auto-filled)
     - **Compiler Version**: Match exactly (e.g., `v0.8.20+commit.a1b79de6`)
     - **Optimization**: `Yes` or `No` (match your compilation settings)
     - **Runs**: If optimization enabled, enter the runs value (e.g., `200`)
     - **License**: Select `MIT` (matches SPDX-License-Identifier in contract)
   - Click **Verify**
   - Wait for verification (usually 30-60 seconds)
   - You'll see a success message with link to Etherscan

4. **Verify Success**:
   - Click the link to view your contract on Etherscan
   - You should see a green checkmark ✓ next to "Contract" tab
   - Source code should be visible and verified

#### Method 2: Manual Verification on Etherscan

1. **Get Contract Source Code**:
   - Open `blockchain/contracts/SOCCS_SYSTEM.sol` from your project
   - Copy the entire file content

2. **Get Compiler Settings from Remix**:
   - In Remix, go to **Solidity Compiler** tab
   - Click **Compilation Details** button
   - Note:
     - **Compiler version** (e.g., `0.8.20`)
     - **Optimization** enabled/disabled
     - **Runs** value if optimization enabled

3. **Navigate to Etherscan**:
   - Go to the appropriate explorer:
     - **Mainnet**: https://etherscan.io
     - **Sepolia**: https://sepolia.etherscan.io
     - **Goerli**: https://goerli.etherscan.io
   - Search for your contract address
   - Click on the contract address

4. **Start Verification**:
   - Click **Contract** tab
   - Click **Verify and Publish** button
   - Select **Via Standard JSON Input** (recommended) or **Via Source Code**

5. **Fill Verification Form**:
   - **Compiler Type**: `Solidity (Single file)` or `Solidity (Standard JSON Input)`
   - **Compiler Version**: Select exact version (e.g., `v0.8.20+commit.a1b79de6`)
   - **Open Source License Type**: `MIT License (MIT)`
   - **Optimization**: `Yes` or `No` (match Remix settings)
   - **Runs**: If optimization enabled, enter runs value

6. **Paste Source Code**:
   - If using "Via Source Code":
     - Paste the entire contract source code
   - If using "Via Standard JSON Input":
     - In Remix, go to **Compilation Details**
     - Copy the entire JSON from **Standard JSON Input** section
     - Paste it in Etherscan

7. **Submit for Verification**:
   - Click **Verify and Publish**
   - Wait for processing (30-60 seconds)
   - You'll see success message when verified

8. **Confirm Verification**:
   - Refresh the contract page on Etherscan
   - Green checkmark ✓ should appear next to "Contract" tab
   - Source code should be visible and readable

#### Important Notes for Verification

- **Compiler Version Must Match**: The version used for verification must exactly match the deployment version
- **Optimization Settings Must Match**: If you compiled with optimization, verify with optimization enabled
- **License Must Match**: Use `MIT` to match the SPDX-License-Identifier in your contract
- **Contract Name**: Use `SOCCS_SYSTEM` (must match contract name in source code)
- **Constructor Arguments**: Not needed for this contract (no constructor parameters)

### 9. Restart Blockchain Signer
1. Navigate to `blockchain/blockchain-signer/` in terminal
2. Stop the current process (Ctrl+C if running)
3. Restart: `npm start` or `node index.js`
4. Verify it connects to the new contract address

## Network Options

### Testnet (Recommended for Testing)
- **Sepolia Testnet**: Get free ETH from faucets
- **Goerli Testnet**: Alternative testnet
- Use testnet RPC URLs from Infura/Alchemy

### Mainnet (Production)
- **Ethereum Mainnet**: Real ETH required
- Higher gas fees
- Only use after thorough testing

## Troubleshooting

### Compilation Errors
- Check Solidity version matches `pragma solidity ^0.8.0`
- Ensure all syntax is correct
- Check for missing semicolons or brackets

### Deployment Fails
- Ensure wallet has enough ETH for gas
- Check network connection
- Verify RPC URL is correct
- Try increasing gas limit

### Contract Not Working
- Verify contract address in `.env` is correct
- Check ABI matches the deployed contract
- Ensure blockchain signer is restarted
- Check network matches (testnet vs mainnet)

### Method Name Shows as Hex (e.g., 0x4a8f7a62)
**Problem**: Blockchain explorer shows hexadecimal values instead of readable function names like `recordTransaction` or `recordBudget`.

**Cause**: The contract is not verified on the blockchain explorer, so it cannot decode function selectors to readable names.

**Solution**: Verify your contract on the blockchain explorer (see Step 8 above). Once verified:
- Function calls will show readable names (e.g., `recordTransaction`, `recordBudget`)
- Transaction details will be human-readable
- Contract source code will be visible and verified

**Quick Fix**:
1. Go to your blockchain explorer (Etherscan, BSCScan, etc.)
2. Navigate to your contract address
3. Click the **Contract** tab
4. Click **Verify and Publish**
5. Follow the verification steps in Step 8 above
6. After verification (30-60 seconds), refresh the page
7. Method names will now display correctly

## Important Notes

⚠️ **Security Warning**: Never commit your `.env` file with real private keys to version control!

✅ **Best Practices**:
- Test on testnet first
- Keep backup of old contract address
- Document deployment date and network
- Save transaction hash for verification

## Post-Deployment Testing

After deployment and verification, test the contract:
1. Check contract is visible on blockchain explorer (Etherscan)
2. Verify source code is verified (green checkmark on Etherscan)
3. Test a transaction (e.g., add expense) through your application
4. Verify transaction hash is returned
5. Check transaction appears on blockchain explorer
6. Verify transaction details on Etherscan match your application logs

