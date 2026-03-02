<?php
// controllers/AuthController.php

// Require the necessary files
require_once 'config/Database.php';
require_once 'models/User.php';
class AuthController {
    private $userModel;

    public function __construct() {
        // Initialize the database and model once for the whole controller
        $database = new Database();
        $db = $database->connect();
        $this->userModel = new User($db);
    }

    // Handles URL: /MVC_Test/auth/register
    public function register() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // The token is missing or doesn't match!
                // Log this attempt, and throw a hard error.
                header('HTTP/1.0 403 Forbidden');
                die("CSRF token validation failed. Unauthorized request.");
            }
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = ($_POST['password']);

            // Basic validation
            if (empty($username) || empty($email) || empty($password)) {
                $error = "Please fill in all fields.";
            }
            else {
                // Check if user already exists
                if ($this->userModel->getUserByEmail($email)) {
                    $error = "Email is already registered.";
                }
                else {
                    // Hash the password securely
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                    // Create the user and get their new ID
                    $new_user_id = $this->userModel->createUser($username, $email, $hashed_password);

                    if ($new_user_id) {
                        // Assign the default "customer" role
                        $this->userModel->assignRoleByName($new_user_id, 'user');

                        header("Location: /UnityExchange/auth/login");
                        exit();
                    }
                    else {
                        $error = "Something went wrong. Please try again.";
                    }
                }
            }
        }

        // Load he view, passing any error messages
        require_once 'includes/header.php';
        require_once 'views/auth/register.php';
        require_once 'includes/footer.php';
    }

    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // The token is missing or doesn't match!
                // Log this attempt, and throw a hard error.
                header('HTTP/1.0 403 Forbidden');
                die("CSRF token validation failed. Unauthorized request.");
            }
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $error = "Please fill in all fields.";
            }
            else {
                // 1. Fetch the user from the database
                $user = $this->userModel->getUserByEmail($email);

                // 2. Verify the user exists AND the password matches the hash
                if ($user && password_verify($password, $user['password_hash'])) {

                    // Regenerate ID on successful login to prevent session fixation
                    session_regenerate_id(true);
                    
                    // Fetch their roles as an array
                    $roles = $this->userModel->getUserRoles($user['id']);

                    // Build the secure session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['roles'] = $roles;
                    $_SESSION['logged_in'] = true;

                    if (in_array('admin', $roles)) {
                        header("Location: /UnityExchange/admin");
                        exit();
                    }
                    else {
                        // Redirect to homepage
                        header("Location: /UnityExchange/home");
                        exit();
                    }
                }
                else {
                    $error = "Invalid email or password";
                }
            }
        }

        // Load he view, passing any error messages
        require_once 'includes/header.php';
        require_once 'views/auth/login.php';
        require_once 'includes/footer.php';
    }

    // Handles URL: /MVC_Test/auth/logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /UnityExchange/home");
        exit();
    }
}