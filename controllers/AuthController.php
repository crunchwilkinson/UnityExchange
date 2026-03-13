<?php
// controllers/AuthController.php

require_once 'config/Database.php';
require_once 'models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->connect();
        $this->userModel = new User($db);
    }

    private function validateCRSF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Security validation failed. Unauthorized request.";
            header("Location: $headerRedirectPath");
            exit();
        }
    }

    // Handles URL: /UnityExchange/auth/register
    public function register() {

        // PRG POST: Process the form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCRSF("/UnityExchange/auth/register");

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Save inputs to session so the user doesn't have to retype them if it fails
            $_SESSION['old_username'] = $username;
            $_SESSION['old_email'] = $email;

            if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
                $_SESSION['flash_error'] = "Please fill in all fields.";
                header("Location: /UnityExchange/auth/register");
                exit();
            }

            if ($password !== $password_confirm) {
                $_SESSION['flash_error'] = "Passwords do not match.";
                header("Location: /UnityExchange/auth/register");
                exit();
            }

            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION['flash_error'] = "Email is already registered.";
                header("Location: /UnityExchange/auth/register");
                exit();
            }

            // Success Path
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $new_user_id = $this->userModel->createUser($username, $email, $hashed_password);

            if ($new_user_id) {
                $this->userModel->assignRoleByName($new_user_id, 'user');
                // Clear old inputs on success
                unset($_SESSION['old_username'], $_SESSION['old_email']); 
                
                $_SESSION['flash_success'] = "Registration successful! You can now log in.";
                header("Location: /UnityExchange/auth/login");
                exit();
            } else {
                $_SESSION['flash_error'] = "Something went wrong. Please try again.";
                header("Location: /UnityExchange/auth/register");
                exit();
            }
        }

        // PRG GET: Grab flash data
        $error = $_SESSION['flash_error'] ?? '';
        $old_username = $_SESSION['old_username'] ?? '';
        $old_email = $_SESSION['old_email'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['old_username'], $_SESSION['old_email']);

        // Load the view (Only reached on GET requests)
        require_once 'includes/header.php';
        require_once 'views/auth/register.php';
        require_once 'includes/footer.php';
    }

    // Handles URL: /UnityExchange/auth/login
    public function login() {

        // PRG POST: Process the form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCRSF("/UnityExchange/auth/login");

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Flash the email back
            $_SESSION['old_email'] = $email;

            if (empty($email) || empty($password)) {
                $_SESSION['flash_error'] = "Please fill in all fields.";
                header("Location: /UnityExchange/auth/login");
                exit();
            }

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Success Path
                session_regenerate_id(true);
                
                $roles = $this->userModel->getUserRoles($user['id']);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['roles'] = $roles;
                $_SESSION['logged_in'] = true;

                // Clear the flashed email so it doesn't linger
                unset($_SESSION['old_email']);

                if (in_array('admin', $roles)) {
                    $_SESSION['flash_success'] = "Welcome back, Admin " . htmlspecialchars($user['username']) . "!";
                    header("Location: /UnityExchange/admin");
                    exit();
                } else {
                    $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['username']) . "!";
                    header("Location: /UnityExchange/product");
                    exit();
                }
            } else {
                $_SESSION['flash_error'] = "Invalid email or password.";
                header("Location: /UnityExchange/auth/login");
                exit();
            }
        }

        // PRG GET: Grab flash data
        $error = $_SESSION['flash_error'] ?? '';
        $old_email = $_SESSION['old_email'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['old_email']);

        // Load the view (Only reached on GET requests)
        require_once 'includes/header.php';
        require_once 'views/auth/login.php';
        require_once 'includes/footer.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /UnityExchange/home");
        exit();
    }
}