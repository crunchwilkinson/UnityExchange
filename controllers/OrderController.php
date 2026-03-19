<?php
// controllers/OrderController.php

require_once 'BaseController.php';
require_once 'models/Order.php';

class OrderController extends BaseController {
    private $orderModel;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();

        $this->orderModel = new Order($this->db);
    }

    // Displays the "My Orders" list for the buyer
    public function index() {
        $user_id = $_SESSION['user_id'];

        // Fetch the orders from the database
        $orders = $this->orderModel->getOrdersByUserId($user_id);

        // Load the views
        require_once 'includes/header.php';
        require_once 'views/order/index.php';
        require_once 'includes/footer.php';
    }


    public function details($order_id) {
        $user_id = $_SESSION['user_id'];

        // 1. Fetch the main order and implicitly check if the buyer owns it
        $order = $this->orderModel->getOrderById($order_id, $user_id);

        if (!$order) {
            $_SESSION['flash_message'] = "Order not found or you do not have permission to view it.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/order");
            exit();
        }

        // 2. Fetch the itemized list
        $items = $this->orderModel->getOrderItems($order_id);

        // 3. Load the view
        require_once 'includes/header.php';
        require_once 'views/order/details.php';
        require_once 'includes/footer.php';
    }

    public function complete($order_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_message'] = "Invalid request method.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/order/details/" . $order_id);
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/order/details/" . $order_id);

        $user_id = $_SESSION['user_id'];

        // Attempt the status update
        if ($this->orderModel->markOrderCompleted($order_id,$user_id)) {
            $_SESSION['flash_message'] = "Thank you for confirming receipt! Your order is now marked as completed.";
            $_SESSION['flash_type'] = "success";
            header("Location: " . $_ENV['APP_URL'] . "/order/details/" . $order_id);
            exit();
        } else {
            $_SESSION['flash_message'] = "Failed to update order status. Please try again.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/order/details/" . $order_id);
            exit();
        }
    }
}