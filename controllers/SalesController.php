<?php
// controllers/SalesController.php

require_once 'BaseController.php';
require_once 'models/Order.php';

class SalesController extends BaseController {
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();

        $this->orderModel = new Order($this->db);
    }

    // URL: unityexchange.great-site.net/sales/index
    // Displays the "My Sales" list for the seller
    public function index() {
        $seller_id = $_SESSION['user_id'];

        // Fetch the sales data for this seller
        $sales = $this->orderModel->getSalesBySellerId($seller_id);

        require_once 'includes/header.php';
        require_once 'views/sales/index.php';
        require_once 'includes/footer.php';
    }
}