<?php
// controllers/CartController.php

require_once 'config/Database.php';
require_once 'models/Product.php';

class CartController {
    private $productModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();

        // Instantiate the Product Model, passing the DB connection
        $this->productModel = new Product($db);

        // Ensure the cart session variable is initialized
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // ==========================
    // HTML VIEW ROUTES
    // ==========================

    // URL: /UnityExchange/cart
    public function index() {
        $cart_items = [];
        $grand_total = 0;

        // Loop through the session cart and fetch the actual product details from the database
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = $this->productModel->getProductById($product_id);

            if ($product) {
                // Ensure the requested quantity doesn't exceed what's currently in stock
                $actual_quantity = min($quantity, $product['stock_quantity']);
                $subtotal = $product['price'] * $actual_quantity;
                
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $actual_quantity,
                    'subtotal' => $subtotal
                ];

                $grand_total += $subtotal;

                // Auto-correct the session if the user had more in their cart than is currently available
                if ($actual_quantity != $quantity) {
                    $_SESSION['cart'][$product_id] = $actual_quantity;
                }
            } else {
                // If the seller deleted the product while it was in the buyer's cart, remove it
                unset($_SESSION['cart'][$product_id]);
            }
        }
        require_once 'includes/header.php';
        require_once 'views/cart/index.php';
        require_once 'includes/footer.php';
    }

    // ==========================================
    // JSON API ENDPOINTS (For JavaScript Fetch)
    // ==========================================

    // Helper method to send JSON responses and stop execution
    private function jsonResponse($status, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
        exit();
    }

    // Helper method to strictly validate Fetch API POST requests
    private function validateApiRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse('error', 'Invalid request method. POST required.');
        }

        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $this->jsonResponse('error', 'CSRF token validation failed.');
        }
    }

    // URL: /UnityExchange/cart/add (expects POST with product_id and quantity)
    public function add() {
        $this->validateApiRequest();

        $product_id = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($quantity <= 0) {
            $this->jsonResponse('error', 'Invalid quantity.');
        }

        $product = $this->productModel->getProductById($product_id);

        if (!$product) {
            $this->jsonResponse('error', 'Product not found.');
        }

        // C2C Rule: Buyers cannot add their own products to their cart
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['user_id']) {
            $this->jsonResponse('error', 'You cannot add your own products to the cart.');
        }

        // Calculate how many items are currently in the cart + the amount being added, and ensure it doesn't exceed stock
        $current_cart_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
        $new_total_quantity = $current_cart_quantity + $quantity;

        if ($new_total_quantity > $product['stock_quantity']) {
            $this->jsonResponse('error', 'Not enough stock available. Seller only has ' . $product['stock_quantity'] . ' left.');
        }

        // Add or update the quantity in the session
        $_SESSION['cart'][$product_id] = $new_total_quantity;

        $total_items_in_cart = array_sum($_SESSION['cart']);
        $this->jsonResponse('success', 'Product added to cart.', ['cart_count' => $total_items_in_cart]);
    }

    // URL: /UnityExchange/cart/update
    public function update() {
        $this->validateApiRequest();

        $product_id = intval($_POST['product_id']);
        $new_quantity = intval($_POST['quantity']);

        if (!isset($_SESSION['cart'][$product_id])) {
            $this->jsonResponse('error', 'Product is not in your cart.');
        }

        if ($new_quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
            $this->jsonResponse('success', 'Product removed from cart.', ['cart_count' => array_sum($_SESSION['cart'])]);
        }

        $product = $this->productModel->getProductById($product_id);

        if (!$product) {
            $this->jsonResponse('error', 'Product not found.');
        }

        if ($new_quantity > $product['stock_quantity']) {
            $this->jsonResponse('error', 'Not enough stock available. Seller only has ' . $product['stock_quantity'] . ' left.');
        }

        $_SESSION['cart'][$product_id] = $new_quantity;

        $this->jsonResponse('success', 'Cart Updated', ['cart_count' => array_sum($_SESSION['cart'])]);
    }

    // URL: /UnityExchange/cart/clear
    public function clear() {
        $this->validateApiRequest();
        
        $_SESSION['cart'] = [];
        
        $this->jsonResponse('success', 'Cart cleared.', ['cart_count' => 0]);
    }
}