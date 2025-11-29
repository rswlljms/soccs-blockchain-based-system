// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract ExpenseTracker {
    struct Transaction {
        string name;
        string description;
        string category;
        string supplier;
        uint256 amount;
        bytes32 methodHash;
        string methodName;
        uint256 timestamp;
    }

    Transaction[] public transactions;

    event MethodExecuted(
        bytes32 indexed methodHash,
        string methodName
    );

    event TransactionRecorded(
        string name,
        string description,
        string category,
        string supplier,
        uint256 amount,
        bytes32 indexed methodHash,
        string methodName,
        uint256 timestamp
    );

    function recordTransaction(
        string memory name,
        string memory description,
        string memory category,
        string memory supplier,
        uint256 amount,
        string memory method
    ) public {
        bytes32 methodHash = keccak256(abi.encodePacked(method));
        
        Transaction memory newTx = Transaction({
            name: name,
            description: description,
            category: category,
            supplier: supplier,
            amount: amount,
            methodHash: methodHash,
            methodName: method,
            timestamp: block.timestamp
        });
        
        transactions.push(newTx);
        
        emit MethodExecuted(methodHash, method);
        emit TransactionRecorded(
            name,
            description,
            category,
            supplier,
            amount,
            methodHash,
            method,
            block.timestamp
        );
    }

    function getTransaction(uint256 index) public view returns (
        string memory name,
        string memory description,
        string memory category,
        string memory supplier,
        uint256 amount,
        bytes32 methodHash,
        string memory methodName,
        uint256 timestamp
    ) {
        require(index < transactions.length, "Transaction index out of bounds");
        Transaction memory transaction = transactions[index];
        return (
            transaction.name,
            transaction.description,
            transaction.category,
            transaction.supplier,
            transaction.amount,
            transaction.methodHash,
            transaction.methodName,
            transaction.timestamp
        );
    }

    function getTransactionCount() public view returns (uint256) {
        return transactions.length;
    }
} 