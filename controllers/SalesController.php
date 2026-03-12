<?php
// controllers/SalesController.php

require_once 'config/Database.php';
require_once 'models/Order.php';
class SalesController {
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();

        $this->orderModel = new Order($db);
    }

    private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash_error'] = "Please log in to view your sales dashboard.";
            header("Location: /UnityExchange/auth/login");
            exit();
        }
    }

    // URL: /UnityExchange/sales
    public function index() {
        $this->requireLogin();

        $seller_id = $_SESSION['user_id'];

        // Fetch the sales data for this seller
        $sales = $this->orderModel->getSalesBySellerId($seller_id);

        require_once 'includes/header.php';
        require_once 'views/sales/index.php';
        require_once 'includes/footer.php';
    }
}