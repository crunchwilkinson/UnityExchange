<?php
// controllers/HomeController.php

// Require the necessary files
require_once 'BaseController.php';
require_once 'models/Product.php';

class HomeController extends BaseController{
    private $productModel;
    public function __construct() {
        parent::__construct();

        // Instantiate the Model, passing the DB connection
        $this->productModel = new Product($this->db);
    }
    
    // URL: unityexchange.great-site.net/home/index
    public function index() {

        // Fetch the real data from MySQL
        if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $products = $this->productModel->getHomeProducts(true, $_SESSION['user_id']);
        } else {
            $products = $this->productModel->getHomeProducts(false);
        }
        
        // Load the Views
        require_once 'includes/header.php';
        require_once 'views/home/index.php';
        require_once 'includes/footer.php';
    }
}