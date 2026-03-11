<?php
// controllers/CheckoutController.php

require_once 'config/Database.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

class CheckoutController {
    private $orderModel;
    private $productModel;

    public function __construct () {
        $database = new Database();
        $db = $database->connect();

        $this->orderModel = new Order($db);
        $this->productModel = new Product($db);
    }

    private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header("Location: /UnityExchange/auth/login");
            exit();
        }
    }

    private function validateCRSF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_error'] = "Security validation failed. Unauthorized request.";
            header("Location: $headerRedirectPath");
            exit();
        }
    }

    // URL: /UnityExchange/checkout
    // Shows the final summary screen before placing the order
    public function index() {
        $this->requireLogin();

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $cart_session = $_SESSION['cart'] ?? [];
        if (empty($cart_session)) {
            $_SESSION['flash_error'] = "Your cart is empty. Please add items to your cart before checking out.";
            header("Location: /UnityExchange/cart");
            exit();
        }

        $cart_items = [];
        $grand_total = 0;

        // Hydrate the cart items from the database using the session IDs
        foreach ($cart_session as $product_id => $quantity) {
            $product = $this->productModel->getProductById($product_id);

            if ($product) {
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
        require_once 'views/checkout/index.php';
        require_once 'includes/footer.php';
    }

    //URL: /UnityExchange/checkout/process
    // The POST route that actually processes the order and saves it to the database
    public function process() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /UnityExchange/checkout");
            exit();
        }

        $this->validateCRSF("/UnityExchange/checkout");

        $cart_session = $_SESSION['cart'] ?? [];
        if (empty($cart_session)) {
            header("Location: /UnityExchange/cart");
            exit();
        }

        $cart_items = [];
        $grand_total = 0;

        // Securely re-calculate everything right before inserting to the database
        foreach ($cart_session as $product_id => $quantity) {
            $product = $this->productModel->getProductById($product_id);
            
            if ($product) {
                // Strict check: if stock is gone, abort the entire checkout
                if ($quantity > $product['stock_quantity']) {
                    $_SESSION['flash_error'] = "Sorry, '" . $product['name'] . "' only has " . $product['stock_quantity'] . " left in stock. Please adjust your cart.";
                    header("Location: /UnityExchange/cart");
                    exit();
                }

                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $grand_total += ($product['price'] * $quantity);
            }
        }

        $user_id = $_SESSION['user_id'];

        // Process the order in a single transaction (in the Order model) to ensure data integrity
        $order_id = $this->orderModel->createOrder($user_id, $cart_items, $grand_total);

        if ($order_id) {
            // Success! Clear the cart and redirect to a confirmation page (not implemented here, but you could easily add one)
            unset($_SESSION['cart']);

            // Redirect to the success receipt page
            header("Location: /UnityExchange/checkout/success/" . $order_id);
            exit();
        } else {
            $_SESSION['flash_error'] = "An error occurred while processing your order. Please try again.";
            header("Location: /UnityExchange/checkout");
            exit();
        }
    }

    // URL: /UnityExchange/checkout/success/5
    public function success($order_id) {
        $this->requireLogin();

        if (!$order_id) {
            header("Location: /UnityExchange/product");
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/checkout/success.php';
        require_once 'includes/footer.php';
    }
}