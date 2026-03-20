<?php
// controllers/AdminController.php

require_once 'BaseController.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Order.php';

class AdminController extends BaseController {
    private $userModel;
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        // Call the BaseController constructor to set up the DB connection
        parent::__construct(); 

        // Ensure the user is logged in and has the 'admin' role before allowing access to any AdminController methods
        $this->requireAdmin();

        $this->userModel = new User($this->db);
        $this->productModel = new Product($this->db);
        $this->orderModel = new Order($this->db);
    }

    // URL: unityexchange.great-site.net/admin/index
    public function index() {
        // Get total counts for dashboard stats
        $total_users = $this->userModel->getTotalUsers();
        $total_products = $this->productModel->getTotalProducts();
        $total_transactions = $this->orderModel->getTotalCompletedOrders();

        require_once 'includes/admin_header.php';
        require_once 'views/admin/index.php';
        require_once 'includes/footer.php';
    }

    // URL: unityexchange.great-site.net/admin/users
    public function users() {
        // Fetch all users with their concatenated roles
        $users = $this->userModel->getAllUsersWithRoles();

        require_once 'includes/admin_header.php';
        require_once 'views/admin/users.php';
        require_once 'includes/footer.php';
    }

    // URL: unityexchange.great-site.net/admin/edit/{id}
    public function edit($id) {
        // Grab any flash messages sent from the update() method
        $error = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_error']); // Clear the flash message after using it

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

    // URL: unityexchange.great-site.net/admin/update/{id}
    public function update($id) {
        // Kick out anyone trying to load this URL directly without submitting the form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $_ENV['APP_URL'] . "/admin/edit/" . $id);
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/admin/edit/" . $id);

        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $role_ids = isset($_POST['roles']) ? $_POST['roles'] : [];

        // Update user details
        $this->userModel->updateUser($id, $username, $email, null);

        // Update the user's roles and set the appropriate flash message
        if ($this->userModel->updateUserRoles($id, $role_ids)) {
            $_SESSION['flash_message'] = "User updated successfully.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Failed to update user roles.";
            $_SESSION['flash_type'] = "error";
        }

        // PRG Pattern: Redirect back to the GET route!
        header("Location: " . $_ENV['APP_URL'] . "/admin/edit/" . $id);
        exit();
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $_ENV['APP_URL'] . "/admin/users");
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/admin/users");

        // Prevent admins from deleting themselves
        if ($_SESSION['user_id'] === $id) {
            $_SESSION['flash_message'] = "You cannot delete your own account.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/admin/users");
            exit();
        }

        if ($this->userModel->deleteUser($id)) {
            $_SESSION['flash_message'] = "User deleted successfully.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_message'] = "Failed to delete user.";
            $_SESSION['flash_type'] = "error";
        }
        header("Location: " . $_ENV['APP_URL'] . "/admin/users");
        exit();
    }

    // URL: unityexchange.great-site.net/admin/products
    public function products() {
        $products = $this->productModel->getAllProducts();

        $categories = $this->productModel->getCategories();
        require_once 'includes/admin_header.php';
        require_once 'views/admin/products.php';
        require_once 'includes/footer.php';
    }

    // URL: unityexchange.great-site.net/admin/products/delete/{id}
    public function deleteProduct($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $_ENV['APP_URL'] . "/admin/products");
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/admin/products");

        $this->productModel->adminDeleteProduct($id);
        $_SESSION['flash_message'] = "Product deleted successfully.";
        $_SESSION['flash_type'] = "success";
        header("Location: " . $_ENV['APP_URL'] . "/admin/products");
        exit();
    }

    // URL: unityexchange.great-site.net/admin/transactions
    public function transactions() {
        $orders = $this->orderModel->getAllOrders();

        require_once 'includes/admin_header.php';
        require_once 'views/admin/transactions.php';
        require_once 'includes/footer.php';
    }

    public function updateOrderStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $_ENV['APP_URL'] . "/admin/transactions");
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/admin/transactions");

        $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';
        
        // Ensure only valid statuses can be injected into the DB
        if (!in_array($new_status, ['pending', 'completed', 'cancelled'])) {
            $_SESSION['flash_message'] = "Invalid status selected.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/admin/transactions");
            exit();
        } 

        if ($this->orderModel->adminUpdateOrderStatus($id, $new_status)) {
            $_SESSION['flash_message'] = "Order #{$id} status updated to " . ucfirst($new_status) . ".";
            $_SESSION['flash_type'] = "success";
            header("Location: " . $_ENV['APP_URL'] . "/admin/transactions");
            exit();
        } else {
            $_SESSION['flash_message'] = "Failed to update order status.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/admin/transactions");
            exit();
        }
    }
}