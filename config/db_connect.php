<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'waste_management';
    private $conn;

    public function connect() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database, 3307);
           
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Display a success message if connected
            echo "Database connected successfully!";
            return $this->conn;
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
?> 