<?php
require_once __DIR__ . '/config.php';

class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
    }

    public function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
            return $this->conn;
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    public function beginTransaction() {
        if (!$this->conn) {
            $this->connect();
        }
        $this->conn->begin_transaction();
    }

    public function commit() {
        if ($this->conn) {
            $this->conn->commit();
        }
    }

    public function rollback() {
        if ($this->conn) {
            $this->conn->rollback();
        }
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    // Added helper methods for better error handling and query execution
    public function prepare($sql) {
        if (!$this->conn) {
            $this->connect();
        }
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $this->conn->error);
        }
        return $stmt;
    }

    public function query($sql) {
        if (!$this->conn) {
            $this->connect();
        }
        $result = $this->conn->query($sql);
        if ($result === false) {
            throw new Exception("Query execution failed: " . $this->conn->error);
        }
        return $result;
    }

    public function escape($value) {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn->real_escape_string($value);
    }

    public function getLastError() {
        return $this->conn ? $this->conn->error : null;
    }

    public function getLastInsertId() {
        return $this->conn ? $this->conn->insert_id : null;
    }
} 