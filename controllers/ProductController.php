<?php
// controllers/ProductController.php

require_once 'config/Database.php';
require_once 'models/Product.php';

class ProductController {
    private $productModel;
    public function __construct() {
        $database = new Database();
        $db = $database->connect();

        // Instantiate the Model, passing the DB connection
        $this->productModel = new Product($db);
    }

    // ==================================
    // PUBLIC ROUTES (NO LOGIN REQUIRED)
    // ==================================

    // URL: /UnityExchange/product OR /UnityExchange/product/index
    public function index() {
        $products = $this->productModel->getAllProducts();

        require_once 'includes/header.php';
        require_once 'views/product/index.php';
        require_once 'includes/footer.php';
    }

    public function details($id) {
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            // Handle case where product is not found
            echo "<h1>404 Error</h1><p>Product not found.</p>";
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/product/details.php';
        require_once 'includes/footer.php';
    }

    // ==================================
    // PROTECTED ROUTES (LOGIN REQUIRED)
    // ==================================

    // Private helper method to enforce login on protected routes
    private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: /UnityExchange/auth/login");
            exit();
        }
    }

    // URL: /UnityExchange/product/create (Shows the form)
    public function create() {
        $this->requireLogin();
        
        require_once 'includes/header.php';
        require_once 'views/product/create.php';
        require_once 'includes/footer.php';
    }

    
    // URL: /UnityExchange/product/store (Processes the form)
    public function store() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF token validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header('HTTP/1.0 403 Forbidden');
                die("CSRF token validation failed. Unauthorized request.");
            }

            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);                     // Ensure price is treated as a float
            $stock_quantity = intval($_POST['stock_quantity']);     // Ensure stock quantity is treated as an integer
            $seller_id = $_SESSION['user_id'];

            $image_filename = 'default_product.png'; // Fallback if no image is uploaded

            // Handle Image Upload Securely
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp_path = $_FILES['product_image']['tmp_name'];
                $file_name = $_FILES['product_image']['name'];
                $file_size = $_FILES['product_image']['size'];

                // Limit file size to 5MB
                if ($file_size > 5000000) {
                    die("File size exceeds the 5MB limit.");
                }

                // Verify MIME type to allow only images
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];
                $file_mime_type = mime_content_type($file_tmp_path);

                if (!in_array($file_mime_type, $allowed_mime_types)) {
                    die("Invalid file type. Only JPEG, PNG, and WebP images are allowed.");
                }

                // Generate a unique filename to prevent overwriting and ensure safe file paths
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $unique_filename = uniqid() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;

                // Move file to the assests/images/products directory
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/UnityExchange/assets/images/products/';
                $destination = $upload_dir . $unique_filename;

                if (move_uploaded_file($file_tmp_path, $destination)) {
                    $image_filename = $unique_filename;
                }
            }

            // Save to Database
            if ($this->productModel->createProduct($seller_id, $name,$description, $image_filename, $price, $stock_quantity)) {
                header("Location: /UnityExchange/product");
                exit();
            } else {
                echo "Error saving product to the database.";
            }
        }
    }

    // URL: /UnityExchange/product/edit/5 (Shows the form)
    public function edit($id) {
        $this->requireLogin();

        $product = $this->productModel->getProductById($id);

        // Security Check: Does this product exist, AND does the logged-in user own it?
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('HTTP/1.0 403 Forbidden');
            echo "<h1>403 Forbidden</h1><p>You do not have permission to edit this product.</p>";
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/products/edit.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/product/update/5 (Processes the form)
    public function update($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF token validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header('HTTP/1.0 403 Forbidden');
                die("CSRF token validation failed. Unauthorized request.");
            }

            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $stock_quantity = intval($_POST['stock_quantity']);
            $seller_id = $_SESSION['user_id'];
            
            $image_filename = null; // Default to null (optional update)

            // Handle Image Upload Securely
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp_path = $_FILES['product_image']['tmp_name'];
                $file_name = $_FILES['product_image']['name'];
                $file_size = $_FILES['product_image']['size'];

                // Limit file size to 5MB
                if ($file_size > 5000000) {
                    die("File size exceeds the 5MB limit.");
                }

                // Verify MIME type to allow only images
                $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];
                $file_mime_type = mime_content_type($file_tmp_path);

                if (!in_array($file_mime_type, $allowed_mime_types)) {
                    die("Invalid file type. Only JPEG, PNG, and WebP images are allowed.");
                }

                // Generate a unique filename to prevent overwriting and ensure safe file paths
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $unique_filename = uniqid() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;

                // Move file to the assests/images/products directory
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/UnityExchange/assets/images/products/';
                $destination = $upload_dir . $unique_filename;

                if (move_uploaded_file($file_tmp_path, $destination)) {
                    $image_filename = $unique_filename;
                }
            }

            // Databae update
            if ($this->productModel->updateProduct($seller_id, $name, $description, $image_filename, $price, $stock_quantity)) {
                header("Location: /UnityExchange/product/details/" . $id);
                exit();
            } else {
                echo "Error updating product in the database.";
            }
        }
    }

    // URL: /UnityExchange/product/delete/5 (Processes the deletion)
    public function delete($id) {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("CSRF validation failed.");
            }

            $seller_id = $_SESSION['user_id'];

            // The Model handles the ownership check via the WHERE clause
            if ($this->productModel->deleteProduct($id, $seller_id)) {
                header("Location: /UnityExchange/product");
                exit();
            } else {
                echo "Error deleting product. It may have already been ordered.";
            }
        }
    }
}