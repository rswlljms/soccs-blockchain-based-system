<?php
date_default_timezone_set('Asia/Manila');

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "soccs_financial_management";
    private $conn;

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET time_zone = '+08:00'");
            return $this->conn;
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
}