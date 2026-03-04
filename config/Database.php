<?php
// config/Database.php

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments starting with #
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split the line into name and value
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove surrounding quotes from the value if present
        $value = trim($value, "\'");

        // Load the variable into the environment
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Load environment variables from the .env file
loadEnv(__DIR__ . '/../.env');
class Database {
    private $host;
    private $db_name;
    private $db_user;
    private $db_password;
    private $conn;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->db_user = $_ENV['DB_USER'];
        $this->db_password = $_ENV['DB_PASS'];
    }

    // Connect to the database using PDO
    public function connect() {
        $this->conn = null;

        try {
            // Define the DSN (Data Source Name) for PDO
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';

            // Create the PDO instance
            $this->conn = new PDO($dsn, $this->db_user, $this->db_password);

            // Set PDO error mode to exception 
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Set default fetch mode to associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If the connection fails, output the error message (for development purposes only - in production, consider logging this instead)
            error_log("Connection Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}