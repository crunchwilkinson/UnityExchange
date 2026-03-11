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
            $_SESSION['flash_error'] = "Please log in to view your order history.";
            header("Location: /UnityExchange/auth/login");
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
}