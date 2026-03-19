<?php
// controllers/AuthController.php

require_once 'BaseController.php';
require_once 'models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();

        $this->userModel = new User($this->db);
    }

    public function register() {

        // PRG POST: Process the form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCSRF($_ENV['APP_URL'] . "/auth/register");

            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

            // Save inputs to session so the user doesn't have to retype them if it fails
            $_SESSION['old_username'] = $username;
            $_SESSION['old_email'] = $email;

            if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
                $_SESSION['flash_error'] = "Please fill in all fields.";
                header("Location: " . $_ENV['APP_URL'] . "/auth/register");
                exit();
            }

            if ($password !== $password_confirm) {
                $_SESSION['flash_message'] = "Passwords do not match.";
                $_SESSION['flash_type'] = "error";
                header("Location: " . $_ENV['APP_URL'] . "/auth/register");
                exit();
            }

            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION['flash_message'] = "Email is already registered.";
                $_SESSION['flash_type'] = "error";
                header("Location: " . $_ENV['APP_URL'] . "/auth/register");
                exit();
            }

            // Success Path
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $new_user_id = $this->userModel->createUser($username, $email, $hashed_password);

            if ($new_user_id) {
                $this->userModel->assignRoleByName($new_user_id, 'user');
                // Clear old inputs on success
                unset($_SESSION['old_username'], $_SESSION['old_email']); 
                
                $_SESSION['flash_message'] = "Registration successful! You can now log in.";
                $_SESSION['flash_type'] = "success";
                header("Location: " . $_ENV['APP_URL'] . "/auth/login");
                exit();
            } else {
                $_SESSION['flash_message'] = "Something went wrong. Please try again.";
                $_SESSION['flash_type'] = "error";
                header("Location: " . $_ENV['APP_URL'] . "/auth/register");
                exit();
            }
        }

        // PRG GET: Grab flash data
        $old_username = $_SESSION['old_username'] ?? '';
        $old_email = $_SESSION['old_email'] ?? '';
        unset($_SESSION['old_username'], $_SESSION['old_email']);

        // Load the view (Only reached on GET requests)
        require_once 'includes/header.php';
        require_once 'views/auth/register.php';
        require_once 'includes/footer.php';
    }

    public function login() {

        // PRG POST: Process the form
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->validateCSRF($_ENV['APP_URL'] . "/auth/login");

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            // Flash the email back
            $_SESSION['old_email'] = $email;

            if (empty($email) || empty($password)) {
                $_SESSION['flash_message'] = "Please fill in all fields.";
                $_SESSION['flash_type'] = "error";
                header("Location: " . $_ENV['APP_URL'] . "/auth/login");
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
                $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default_avatar.png';

                // Clear the flashed email so it doesn't linger
                unset($_SESSION['old_email']);

                if (in_array('admin', $roles)) {
                    $_SESSION['flash_message'] = "Welcome back, Admin " . htmlspecialchars($user['username']) . "!";
                    $_SESSION['flash_type'] = "success";
                    header("Location: " . $_ENV['APP_URL'] . "/admin");
                    exit();
                } else {
                    $_SESSION['flash_message'] = "Welcome back, " . htmlspecialchars($user['username']) . "!";
                    $_SESSION['flash_type'] = "success";
                    header("Location: " . $_ENV['APP_URL'] . "/product");
                    exit();
                }
            } else {
                $_SESSION['flash_message'] = "Invalid email or password.";
                $_SESSION['flash_type'] = "error";
                header("Location: " . $_ENV['APP_URL'] . "/auth/login");
                exit();
            }
        }

        // PRG GET: Grab flash data
        $old_email = $_SESSION['old_email'] ?? '';
        unset($_SESSION['old_email']);

        // Load the view (Only reached on GET requests)
        require_once 'includes/header.php';
        require_once 'views/auth/login.php';
        require_once 'includes/footer.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: " . $_ENV['APP_URL'] . "/home");
        exit();
    }
}