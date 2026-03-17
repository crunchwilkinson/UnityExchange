<?php
// controllers/OrderController.php

require_once 'config/Database.php';
require_once 'models/Order.php';

class OrderController {
    private $orderModel;

    public function __construct() {
        $database = new Database();
        $db = $database->connect();
        $this->orderModel = new Order($db);
    }

    // Security check
    private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: /UnityExchange/auth/login");
            exit();
        }
    }

    private function validateCRSF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_message'] = "Security validation failed. Unauthorized request.";
            $_SESSION['flash_type'] = "error";
            header("Location: $headerRedirectPath");
            exit();
        }
    }

    // URL: /UnityExchange/order
    // Displays the "My Orders" list for the buyer
    public function index() {
        $this->requireLogin();

        $user_id = $_SESSION['user_id'];

        // Fetch the orders from the database
        $orders = $this->orderModel->getOrdersByUserId($user_id);

        // Load the views
        require_once 'includes/header.php';
        require_once 'views/order/index.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/order/details/5
    public function details($order_id) {
        $this->requireLogin();

        $user_id = $_SESSION['user_id'];

        // 1. Fetch the main order and implicitly check if the buyer owns it
        $order = $this->orderModel->getOrderById($order_id, $user_id);

        if (!$order) {
            $_SESSION['flash_message'] = "Order not found or you do not have permission to view it.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/order");
            exit();
        }

        // 2. Fetch the itemized list
        $items = $this->orderModel->getOrderItems($order_id);

        // 3. Load the view
        require_once 'includes/header.php';
        require_once 'views/order/details.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/order/complete/5
    public function complete($order_id) {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_message'] = "Invalid request method.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/order/details/" . $order_id);
            exit();
        }

        $this->validateCRSF("/UnityExchange/order/details/" . $order_id);

        $user_id = $_SESSION['user_id'];

        // Attempt the status update
        if ($this->orderModel->markOrderCompleted($order_id,$user_id)) {
            $_SESSION['flash_message'] = "Thank you for confirming receipt! Your order is now marked as completed.";
            $_SESSION['flash_type'] = "success";
            header("Location: /UnityExchange/order/details/" . $order_id);
            exit();
        } else {
            $_SESSION['flash_message'] = "Failed to update order status. Please try again.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/order/details/" . $order_id);
            exit();
        }
    }
}