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

### 4. Get the ABI
1. After successful compilation, click **Compilation Details** button
2. In the popup, scroll to find **ABI** section
3. Click the **copy icon** next to the ABI JSON
4. **Save this** - you may need to update `blockchain/blockchain-signer/abi.json`

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

### 8. Restart Blockchain Signer
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

## Important Notes

⚠️ **Security Warning**: Never commit your `.env` file with real private keys to version control!

✅ **Best Practices**:
- Test on testnet first
- Keep backup of old contract address
- Document deployment date and network
- Save transaction hash for verification

## Verification

After deployment, test the contract:
1. Check contract is visible on blockchain explorer (Etherscan)
2. Test a transaction (e.g., add expense) through your application
3. Verify transaction hash is returned
4. Check transaction appears on blockchain explorer

