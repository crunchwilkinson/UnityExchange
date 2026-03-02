<?php
// config/Database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'unityexchange';
    private $db_user = 'root';
    private $db_password = '';
    private $conn;

    // Connect to the database using PDO
    public function connect() {
        $this->conn = null;

        try {
            // Define the DSN (Data Source Name) for PDO
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';

            // Create the PDO instance
            $this->conn = new PDO($dsn, $this->db_user, $this->db_password);

            // Set PDO error mode to exception 
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Set default fetch mode to associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If the connection fails, output the error message (for development purposes only - in production, consider logging this instead)
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}