<?php
// controllers/BaseController.php

require_once 'config/Database.php';

class BaseController {
    // Protected means this controller and any child controller can access it
    protected $db;

    public function __construct() {
        // Automatically connect to the database whenever a controller is created
        $database = new Database();
        $this->db = $database->connect();
    }

    /**
     * Ensures the user is logged in
     */
    protected function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: " . $_ENV['APP_URL'] . "/auth/login");
            exit();
        }
    }

    /**
     * Protects POST routes from Cross-Site Request Forgery
     */
    protected function validateCSRF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_message'] = "Security validation failed. Unauthorized request.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $headerRedirectPath);
            exit();
        }
    }

    /**
     * Ensures the user has an admin role
     */
    protected function requireAdmin() {
        // Automatically implies they must be logged in first
        $this->requireLogin();

        if (!isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            header('HTTP/1.0 403 Forbidden');
            echo "<div style='background-color: #fed7d7;color: #822727;border: 1px solid #feb2b2; margin: 0 auto; margin-bottom: 20px; text-align: center; padding: 20px;'><h1>403 Forbidden</h1><p>You do not have administrative access.</p></div>";
            exit();
        }
    }
}