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
        // Access control: Only allow users with the 'admin' role to access any method in this controller
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

    private function validateCRSF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Security validation failed. Unauthorized request.";
            header("Location: $headerRedirectPath");
            exit();
        }
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

    // URL: /UnityExchange/admin/edit/5
    // STRICTLY for displaying the HTML form
    public function edit($id) {
        // Grab any flash messages sent from the update() method
        $error = $_SESSION['flash_error'] ?? '';
        $success = $_SESSION['flash_success'] ?? '';
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

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

    // URL: /UnityExchange/admin/update/5
    // STRICTLY for processing the POST request
    public function update($id) {
        // Kick out anyone trying to load this URL directly without submitting the form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /UnityExchange/admin/edit/" . $id);
            exit();
        }

        $this->validateCRSF("/UnityExchange/admin/edit/" . $id);

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role_ids = isset($_POST['roles']) ? $_POST['roles'] : [];

        // Update user details
        $this->userModel->updateUser($id, $username, $email);

        // Update the user's roles and set the appropriate flash message
        if ($this->userModel->updateUserRoles($id, $role_ids)) {
            $_SESSION['flash_success'] = "User updated successfully.";
        } else {
            $_SESSION['flash_error'] = "Failed to update user roles.";
        }

        // PRG Pattern: Redirect back to the GET route!
        header("Location: /UnityExchange/admin/edit/" . $id);
        exit();
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /UnityExchange/admin/users");
            exit();
        }

        $this->validateCRSF("/UnityExchange/admin/users");

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
            header("Location: /UnityExchange/admin/products");
            exit();
        }

        $this->validateCRSF("/UnityExchange/admin/products");

        $this->productModel->adminDeleteProduct($id);
        $_SESSION['flash_success'] = "Product deleted successfully.";
        header("Location: /UnityExchange/admin/products");
        exit();
    }
}