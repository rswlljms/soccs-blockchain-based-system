const express = require('express');
const Web3 = require('web3');
const bodyParser = require('body-parser');
const cors = require('cors');
require('dotenv').config();
const abi = require('./abi.json');

//environment logging
console.log('Environment Configuration:');
console.log('Contract Address:', process.env.CONTRACT_ADDRESS);
console.log('Infura URL:', process.env.INFURA_URL ? 'Configured' : 'Missing');
console.log('Private Key:', process.env.PRIVATE_KEY ? 'Configured' : 'Missing');

const app = express();
app.use(cors());
app.use(bodyParser.json()); // Parse JSON data

//test route
app.get('/', (req, res) => {
  res.json({
    message: 'Blockchain signer service is running',
    contractAddress: process.env.CONTRACT_ADDRESS
  });
});

const web3 = new Web3(process.env.INFURA_URL);

// Log the contract address being used
console.log('Using contract address:', process.env.CONTRACT_ADDRESS);

const contract = new web3.eth.Contract(abi, process.env.CONTRACT_ADDRESS);

const account = web3.eth.accounts.privateKeyToAccount(process.env.PRIVATE_KEY);
web3.eth.accounts.wallet.add(account);

app.post('/add-expenses', async (req, res) => {
  console.log('Received expense data:', req.body);
  const { name, description, amount, category, supplier, method } = req.body;

  try {
    console.log('Attempting to record transaction...');
    console.log('Using contract address:', process.env.CONTRACT_ADDRESS);

    // Convert amount to Wei
    const amountInWei = web3.utils.toWei(amount.toString(), 'ether');

    // Create transaction data - pass method string directly
    const data = contract.methods.recordTransaction(
      name,
      description,
      category,
      supplier,
      amountInWei,
      method || 'EXPENSE' // Pass method string directly
    ).encodeABI();

    // Log transaction details
    console.log('Transaction details:');
    console.log('From address:', account.address);
    console.log('To contract:', process.env.CONTRACT_ADDRESS);
    console.log('Method:', method || 'EXPENSE');

    // Send transaction
    const tx = await web3.eth.sendTransaction({
      from: account.address,
      to: process.env.CONTRACT_ADDRESS,
      gas: 300000,
      data: data
    });

    console.log('Transaction successful:', tx.transactionHash);
    res.json({
      status: 'success',
      txHash: tx.transactionHash,
      contractAddress: process.env.CONTRACT_ADDRESS
    });
  } catch (err) {
    console.error('Blockchain error:', err);
    res.status(500).json({
      status: 'error',
      message: err.message,
      details: err.toString(),
      contractAddress: process.env.CONTRACT_ADDRESS
    });
  }
});

const PORT = 3001;
app.listen(PORT, () => {
  console.log(`Blockchain signer service running on http://localhost:${PORT}`);
  console.log('Contract address:', process.env.CONTRACT_ADDRESS);
});
