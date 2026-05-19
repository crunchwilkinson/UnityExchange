<?php
// controllers/CheckoutController.php

require_once 'BaseController.php';
require_once 'models/Order.php';
require_once 'models/Product.php';

class CheckoutController extends BaseController {
    private $orderModel;
    private $productModel;

    public function __construct () {
        parent::__construct ();
        $this->requireLogin();

        $this->orderModel = new Order($this->db);
        $this->productModel = new Product($this->db);
    }

    // URL: unityexchange.great-site.net/checkout/index
    // Shows the final summary screen before placing the order
    public function index() {

        $cart_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart_session)) {
            $_SESSION['flash_message'] = "Your cart is empty. Please add items to your cart before checking out.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/cart");
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

    private function generatePayFastSignature($data, $passPhrase = null) {
    // Create parameter string
    $pfOutput = '';
    foreach( $data as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode(trim($val)) .'&';
        }
    }
    // Remove last ampersand
    $getString = substr($pfOutput, 0, -1);
    
    // Append passphrase if exists
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode(trim($passPhrase));
    }
    return md5($getString);
}

    // URL: unityexchange.great-site.net/checkout/process
    // The POST route that actually processes the order and saves it to the database
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $_ENV['APP_URL'] . "/checkout");
            exit();
        }

        $this->validateCSRF($_ENV['APP_URL'] . "/checkout");

        $cart_session = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart_session)) {
            header("Location: " . $_ENV['APP_URL'] . "/cart");
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
                    $_SESSION['flash_message'] = "Sorry, '" . $product['name'] . "' only has " . $product['stock_quantity'] . " left in stock. Please adjust your cart.";
                    $_SESSION['flash_type'] = "error";
                    header("Location: " . $_ENV['APP_URL'] . "/cart");
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

            // --- PAYFAST REDIRECTION LOGIC ---
            
            // Determine Sandbox or Live URL
            $isTest = filter_var($_ENV['PF_TEST_MODE'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $payfast_url = $isTest ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process';
            
            // Build the data array required by PayFast
            $data = [
                'merchant_id' => $_ENV['PF_MERCHANT_ID'],
                'merchant_key' => $_ENV['PF_MERCHANT_KEY'],
                'return_url' => $_ENV['APP_URL'] . '/checkout/success/' . $order_id,
                'cancel_url' => $_ENV['APP_URL'] . '/cart',
                'notify_url' => $_ENV['APP_URL'] . '/checkout/itn',
                'name_first' => $_SESSION['username'], // Using session username
                'm_payment_id' => $order_id,           // Your database Order ID
                'amount' => number_format(sprintf('%.2f', $grand_total), 2, '.', ''),
                'item_name' => 'UnityExchange Order #' . $order_id
            ];

            // Generate the security signature
            $data['signature'] = $this->generatePayFastSignature($data, $_ENV['PF_PASSPHRASE'] ?? null);

            // Output an invisible form and use JavaScript to submit it instantly
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h2>Transferring you to PayFast's secure checkout...</h2>";
            echo "<form id='payfast-form' action='$payfast_url' method='POST'>";
            foreach ($data as $name => $value) {
                echo "<input type='hidden' name='$name' value='" . htmlspecialchars($value) . "'>";
            }
            echo "</form>";
            echo "</div>";
            echo "<script>document.getElementById('payfast-form').submit();</script>";
            exit();

        } else {
            $_SESSION['flash_message'] = "An error occurred while processing your order.";
            $_SESSION['flash_type'] = "error";
            header("Location: " . $_ENV['APP_URL'] . "/checkout");
            exit();
        }
    }

    // URL: unityexchange.great-site.net/checkout/itn
    public function itn() {
        // CRITICAL: Do NOT call $this->requireLogin() or $this->validateCSRF() here!
        // This request comes from PayFast's servers, not your logged-in user.

        // 1. Strip the signature from the POST data
        $pfData = $_POST;
        $signature = $pfData['signature'] ?? '';
        unset($pfData['signature']);

        // 2. Re-create the signature using your passphrase to verify it matches
        $expectedSignature = $this->generatePayFastSignature($pfData, $_ENV['PF_PASSPHRASE'] ?? null);

        if ($signature !== $expectedSignature) {
            http_response_code(400); // Bad Request
            die('Invalid signature');
        }

        // 3. Optional but recommended: Verify the IP address belongs to PayFast here.

        // 4. Update your database if the payment is complete
        if (isset($pfData['payment_status']) && $pfData['payment_status'] === 'COMPLETE') {
            $order_id = $pfData['m_payment_id'];
            
            // Fortunately, you already have an admin Update method in your Order model 
            // that bypasses the strict user_id checks. We can reuse it here!
            $this->orderModel->adminUpdateOrderStatus($order_id, 'paid');
        }

        // 5. Always return a 200 OK so PayFast knows you received the message
        http_response_code(200);
        exit();
    }

    // URL: unityexchange.great-site.net/checkout/success/{order_id}
    // A simple confirmation page that thanks the user for their purchase and shows the order ID
    public function success($order_id) {
        $this->requireLogin();

        if (!$order_id) {
            header("Location: " . $_ENV['APP_URL'] . "/product");
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/checkout/success.php';
        require_once 'includes/footer.php';
    }
}