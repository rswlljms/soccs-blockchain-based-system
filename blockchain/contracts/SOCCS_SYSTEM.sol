// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract SOCCS_SYSTEM {
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

    struct Budget {
        string source;
        string description;
        uint256 amount;
        uint256 timestamp;
    }

    struct Vote {
        uint256 electionId;
        string voterId;
        uint256 candidateId;
        uint256 positionId;
        uint256 timestamp;
    }

    struct ElectionConfirmation {
        uint256 electionId;
        string electionTitle;
        uint256 totalVotes;
        uint256 timestamp;
    }

    Transaction[] public transactions;
    Budget[] public budgets;
    Vote[] public votes;
    ElectionConfirmation[] public electionConfirmations;

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

    event BudgetRecorded(
        string source,
        string description,
        uint256 amount,
        uint256 timestamp
    );

    event VoteRecorded(
        uint256 indexed electionId,
        string voterId,
        uint256 candidateId,
        uint256 positionId,
        uint256 timestamp
    );

    event ElectionConfirmed(
        uint256 indexed electionId,
        string electionTitle,
        uint256 totalVotes,
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

    function recordBudget(
        string memory source,
        string memory description,
        uint256 amount
    ) public {
        Budget memory newBudget = Budget({
            source: source,
            description: description,
            amount: amount,
            timestamp: block.timestamp
        });
        
        budgets.push(newBudget);
        
        emit BudgetRecorded(
            source,
            description,
            amount,
            block.timestamp
        );
    }

    function recordVote(
        uint256 electionId,
        string memory voterId,
        uint256 candidateId,
        uint256 positionId,
        string memory method
    ) public {
        bytes32 methodHash = keccak256(abi.encodePacked(method));
        
        Vote memory newVote = Vote({
            electionId: electionId,
            voterId: voterId,
            candidateId: candidateId,
            positionId: positionId,
            timestamp: block.timestamp
        });
        
        votes.push(newVote);
        
        emit MethodExecuted(methodHash, method);
        emit VoteRecorded(
            electionId,
            voterId,
            candidateId,
            positionId,
            block.timestamp
        );
    }

    function recordBatchVotes(
        uint256 electionId,
        string memory voterId,
        uint256[] memory candidateIds,
        uint256[] memory positionIds,
        string memory method
    ) public {
        require(candidateIds.length == positionIds.length, "Arrays length mismatch");
        require(candidateIds.length > 0, "No votes to record");
        
        bytes32 methodHash = keccak256(abi.encodePacked(method));
        
        for (uint256 i = 0; i < candidateIds.length; i++) {
            Vote memory newVote = Vote({
                electionId: electionId,
                voterId: voterId,
                candidateId: candidateIds[i],
                positionId: positionIds[i],
                timestamp: block.timestamp
            });
            
            votes.push(newVote);
            
            emit VoteRecorded(
                electionId,
                voterId,
                candidateIds[i],
                positionIds[i],
                block.timestamp
            );
        }
        
        emit MethodExecuted(methodHash, method);
    }

    function confirmElection(
        uint256 electionId,
        string memory electionTitle,
        uint256 totalVotes,
        string memory method
    ) public {
        bytes32 methodHash = keccak256(abi.encodePacked(method));
        
        ElectionConfirmation memory confirmation = ElectionConfirmation({
            electionId: electionId,
            electionTitle: electionTitle,
            totalVotes: totalVotes,
            timestamp: block.timestamp
        });
        
        electionConfirmations.push(confirmation);
        
        emit MethodExecuted(methodHash, method);
        emit ElectionConfirmed(
            electionId,
            electionTitle,
            totalVotes,
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

    function getBudget(uint256 index) public view returns (
        string memory source,
        string memory description,
        uint256 amount,
        uint256 timestamp
    ) {
        require(index < budgets.length, "Budget index out of bounds");
        Budget memory budget = budgets[index];
        return (
            budget.source,
            budget.description,
            budget.amount,
            budget.timestamp
        );
    }

    function getVote(uint256 index) public view returns (
        uint256 electionId,
        string memory voterId,
        uint256 candidateId,
        uint256 positionId,
        uint256 timestamp
    ) {
        require(index < votes.length, "Vote index out of bounds");
        Vote memory vote = votes[index];
        return (
            vote.electionId,
            vote.voterId,
            vote.candidateId,
            vote.positionId,
            vote.timestamp
        );
    }

    function getElectionConfirmation(uint256 index) public view returns (
        uint256 electionId,
        string memory electionTitle,
        uint256 totalVotes,
        uint256 timestamp
    ) {
        require(index < electionConfirmations.length, "Election confirmation index out of bounds");
        ElectionConfirmation memory confirmation = electionConfirmations[index];
        return (
            confirmation.electionId,
            confirmation.electionTitle,
            confirmation.totalVotes,
            confirmation.timestamp
        );
    }

    function getTransactionCount() public view returns (uint256) {
        return transactions.length;
    }

    function getBudgetCount() public view returns (uint256) {
        return budgets.length;
    }

    function getVoteCount() public view returns (uint256) {
        return votes.length;
    }

    function getElectionConfirmationCount() public view returns (uint256) {
        return electionConfirmations.length;
    }
}

