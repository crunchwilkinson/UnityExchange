<?php
// controllers/HomeController.php

// Require the necessary files
require_once 'config/Database.php';
require_once 'models/Product.php';

class HomeController {
    private $productModel;
    public function __construct() {
        $database = new Database();
        $db = $database->connect();

        // Instantiate the Model, passing the DB connection
        $this->productModel = new Product($db);
    }
    
    public function index() {

        // Fetch the real data from MySQL
        $products = $this->productModel->getAllProducts();

        // Load the Views
        require_once 'includes/header.php';
        require_once 'views/home/index.php';
        require_once 'includes/footer.php';
    }
}