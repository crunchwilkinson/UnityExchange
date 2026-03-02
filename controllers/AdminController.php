<?php
// controllers/AdminController.php

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Product.php';

class AdminController {
    private $userModel;
    private $productModel;

    public function __construct()
    {
        if (!isset($_SESSION['logged_in']) || !isset(($_SESSION['roles'])) || !in_array('admin', $_SESSION['roles'])) {
            header('HTTP/1.0 403 Forbidden');
            echo "<div style='background-color: #fed7d7;color: #822727;border: 1px solid #feb2b2; margin: 0 auto; margin-bottom: 20px;'><h1>403 Forbidden</h1><p>You do not have administrative access.</p></div>";
            exit();
        }

        $database = new Database();
        $db = $database->connect();
        $this->userModel = new User($db);
        $this->productModel = new Product($db);
    }

    public function index() {
        require_once 'includes/admin_header.php';
        require_once 'views/admin/index.php';
        require_once 'includes/footer.php';
    }

    public function users() {
        // Fetch all users with their concatenated roles
        $users = $this->userModel->getAllUsersWithRoles();

        require_once 'includes/admin_header.php';
        require_once 'views/admin/users.php';
        require_once 'includes/footer.php';
    }

    public function edit($id) {
        $error = '';
        $success = '';

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // The token is missing or doesn't match!
                // Log this attempt, and throw a hard error.
                header('HTTP/1.0 403 Forbidden');
                die("CSRF token validation failed. Unauthorized request.");
            }

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);

            // role_ids will be an array passed from HTML checkboxes
            $role_ids = isset($_POST['roles']) ? $_POST['roles'] : [];

            // Update user details and roles
            $this->userModel->updateUser($id, $username, $email);

            // Update the user's roles
            if ($this->userModel->updateUserRoles($id, $role_ids)) {
                $success = "User updated successfully.";
            } else {
                $error = "Failed to update user roles.";
            }
        }

        // Fetch data for the View to display
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            echo "User not found.";
            exit();
        }

        // Get all possible roles to generate the checkboxes
        $all_roles = $this->userModel->getAllAvailableRoles();

        // Get the specific roles this user currently has, to pre-check the boxes
        $user_current_roles = $this->userModel->getUserRoles($id);

        require_once 'includes/admin_header.php';
        require_once 'views/admin/edit.php';
        require_once 'includes/footer.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            header('HTTP/1.0 403 Forbidden');
            die("CSRF token validation failed. Unauthorized request.");
        }

        // Prevent admins from deleting themselves
        if ($_SESSION['user_id'] != $id) {
            $this->userModel->deleteUser($id);
        }
        header("Location: /UnityExchange/admin/users");
        exit();
    }

    public function products() {
        $products = $this->productModel->getAllProducts();

        require_once 'includes/admin_header.php';
        require_once 'views/admin/products.php';
        require_once 'includes/footer.php';
    }

    public function deleteProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            exit();
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            header('HTTP/1.0 403 Forbidden');
            die("CSRF token validation failed. Unauthorized request.");
        }

        $this->productModel->adminDeleteProduct($id);
        header("Location: /UnityExchange/admin/products");
        exit();
    }
}