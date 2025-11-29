<?php
require_once __DIR__ . '/database.php';

class ExpenseOperations {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        if (!$this->conn) {
            throw new Exception("Database connection failed");
        }
    }

    public function addExpense($data) {
        try {
            error_log("Adding expense with data: " . print_r($data, true));
            
            $query = "INSERT INTO expenses (name, amount, category, description, supplier, document, date, transaction_hash) 
                     VALUES (:name, :amount, :category, :description, :supplier, :document, :date, :transaction_hash)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":amount", $data['amount']);
            $stmt->bindParam(":category", $data['category']);
            $stmt->bindParam(":description", $data['description']);
            $stmt->bindParam(":supplier", $data['supplier']);
            $stmt->bindParam(":document", $data['document']);
            $stmt->bindParam(":date", $data['date']);
            $stmt->bindParam(":transaction_hash", $data['transaction_hash']);
            
            $result = $stmt->execute();
            error_log("Query execution result: " . ($result ? "success" : "failed"));
            return $result;
        } catch(PDOException $e) {
            error_log("Database error in addExpense: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getExpenses($page = 1, $limit = 6, $category = null) {
        try {
            $offset = ($page - 1) * $limit;
            
            $query = "SELECT SQL_CALC_FOUND_ROWS * FROM expenses";
            
            if ($category !== null) {
                $query .= " WHERE category = :category";
            }
            
            $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            if ($category !== null) {
                $stmt->bindParam(':category', $category);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $this->conn->query("SELECT FOUND_ROWS()")->fetchColumn();

            return [
                'expenses' => $expenses,
                'total' => (int)$total
            ];
        } catch(PDOException $e) {
            error_log("Database error in getExpenses: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getExpensesSummary($category = null) {
        try {
            $whereClause = $category && $category !== 'All' ? 'WHERE category = :category' : '';
            
            $query = "SELECT 
                COUNT(*) as total_count,
                COALESCE(SUM(amount), 0) as total_amount,
                (SELECT category FROM expenses GROUP BY category ORDER BY SUM(amount) DESC LIMIT 1) as top_category
                FROM expenses $whereClause";
            
            $stmt = $this->conn->prepare($query);
            
            if ($category && $category !== 'All') {
                $stmt->bindParam(':category', $category);
            }
            
            $stmt->execute();
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_count' => (int)$summary['total_count'],
                'total_amount' => (float)$summary['total_amount'],
                'top_category' => $summary['top_category'] ?? '-'
            ];
        } catch(PDOException $e) {
            error_log("Database error in getExpensesSummary: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
?> 