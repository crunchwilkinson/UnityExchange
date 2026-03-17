<?php
// controllers/ProductController.php

// Pull in the database configuration and the Product model
require_once 'config/Database.php';
require_once 'models/Product.php';

class ProductController {
    private $productModel;
    
    // The constructor runs automatically when the controller is called
    public function __construct() {
        // Establish the database connection
        $database = new Database();
        $db = $database->connect();
        
        // Instantiate the Product model and pass the connection to it
        $this->productModel = new Product($db);
    }

    // ==================================
    // PRIVATE HELPER METHODS
    // ==================================

    // Helper: Checks if a user is logged in, kicks them to login page if not
    private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash_message'] = "Please log in to create/modify/view your listings.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/auth/login");
            exit();
        }
    }

    private function validateCRSF($headerRedirectPath) {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['flash_message'] = "Security validation failed. Unauthorized request.";
            $_SESSION['flash_type'] = "error";
            header("Location: $headerRedirectPath");
            exit();
        }
    }

    // Helper: Handles secure image uploading, size limits, and MIME type checks.
    // The $isRequired parameter allows us to force an upload for new products, 
    // but keep it optional when editing existing products.
    private function handleImageUpload($fileArray, $isRequired = false) {
        // 1. Check if the user completely skipped the file upload
        if (!isset($fileArray) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
            if ($isRequired) {
                // Return an error if this is a new product creation
                return ['success' => false, 'error' => "An image is required. Please upload a photo of your item."];
            } else {
                // If it's an update, it's okay to skip the image upload
                return ['success' => true, 'filename' => null]; 
            }
        }

        // 2. Check for other network/server upload errors
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => "A network error occurred during file upload."];
        }

        // Extract file details
        $file_tmp_path = $fileArray['tmp_name'];
        $file_name = $fileArray['name'];
        $file_size = $fileArray['size'];
        
        // 3. Security: Enforce a strict 5MB file size limit
        if ($file_size > 5000000) {
            return ['success' => false, 'error' => "File size exceeds the 5MB limit. Please choose a smaller image."];
        }

        // 4. Security: Verify the actual MIME type to prevent fake extensions (e.g., virus.php renamed to virus.jpg)
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array(mime_content_type($file_tmp_path), $allowed_mime_types)) {
            return ['success' => false, 'error' => "Invalid file type. Only JPEG, PNG, and WEBP images are allowed."];
        }

        // 5. Generate a completely unique, random filename so users don't overwrite each other's photos
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_filename = uniqid() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
        
        // Define where the file should be saved
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/UnityExchange/assets/images/products/' . $unique_filename;

        // 6. Move the file from temporary memory to the permanent assets folder
        if (move_uploaded_file($file_tmp_path, $destination)) {
            return ['success' => true, 'filename' => $unique_filename];
        }

        // Failsafe if the server folder lacks write permissions
        return ['success' => false, 'error' => "Server error: Failed to save the uploaded image to the directory."];
    }

    // ==================================
    // PUBLIC ROUTES (NO LOGIN REQUIRED)
    // ==================================

    // URL: /UnityExchange/product OR /UnityExchange/product/index
    // Displays the main marketplace catalog
    public function index() {
        // Grab the search term if it exists, otherwise set to null
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : null;

        // Fetch the filtered products 
        if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $products = $this->productModel->getLatestProducts(true, $_SESSION['user_id'], $search_term);
        } else {
            $products = $this->productModel->getLatestProducts(false, null, $search_term);
        }

        // Fetch categories to populate the filter buttons on the catalog page
        $categories = $this->productModel->getCategories();
        
        require_once 'includes/header.php';
        require_once 'views/product/index.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/product/details/5
    // Displays the specific details for a single product
    public function details($id) {
        $product = $this->productModel->getProductById($id);
        
        if (!$product) {
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

    // URL: /UnityExchange/product/create 
    // Shows the blank form to list a new item
    public function create() {
        $this->requireLogin();

        // Fetch categories to populate the HTML dropdown menu
        $categories = $this->productModel->getCategories();
        
        require_once 'includes/header.php';
        require_once 'views/product/create.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/product/store 
    // Captures the form submission from create() and saves to the database
    public function store() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Validate the CSRF token to prevent cross-site request forgery
            $this->validateCRSF("/UnityExchange/product/create");

            // 2. Sanitize and extract the text inputs
            $name =  isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
            $stock_quantity = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
            $seller_id = $_SESSION['user_id'];
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
            
            // 3. Process the image upload (Passing 'true' makes the upload mandatory!)
            $uploadResult = $this->handleImageUpload($_FILES['product_image'] ?? null, true);
            
            // If the upload failed (or was empty), send the error back to the form
            if (!$uploadResult['success']) {
                $_SESSION['flash_message'] = $uploadResult['error'];
                $_SESSION['flash_type'] = "error";
                header("Location: /UnityExchange/product/create");
                exit();
            }

            // The file uploaded successfully, grab the new randomized filename
            $image_filename = $uploadResult['filename'];

            // 4. Save everything to the database
            if ($this->productModel->createProduct($seller_id, $category_id, $name, $description, $image_filename, $price, $stock_quantity)) {
                
                // Set the session flash message to show on the next page load (PRG Pattern)
                $_SESSION['flash_message'] = "Product listed successfully! It may take a few moments to appear in the marketplace catalog.";
                $_SESSION['flash_type'] = "success";

                // Success! Redirect the user back to the marketplace catalog
                header("Location: /UnityExchange/product");
                exit();
            } else {
                // Database failed
                $_SESSION['flash_message'] = "Database error: Could not save your product. Please try again.";
                $_SESSION['flash_type'] = "error";
                header("Location: /UnityExchange/product/create");
                exit();
            }
        } else {
            // If not POST, redirect back to the create page
            header("Location: /UnityExchange/product/create");
            exit();
        }
    }

    // URL: /UnityExchange/product/edit/5
    // Shows the pre-filled form to edit an existing item
    public function edit($id) {
        $this->requireLogin();

        // Fetch the product first to ensure it exists
        $product = $this->productModel->getProductById($id);

        // Security Check: Ensure the logged-in user is actually the owner of this product
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('HTTP/1.0 403 Forbidden');
            die("<h1>403 Forbidden</h1><p>You do not have permission to edit this product.</p>");
        }

        // Fetch categories to populate the HTML dropdown menu
        $categories = $this->productModel->getCategories();

        require_once 'includes/header.php';
        require_once 'views/product/edit.php';
        require_once 'includes/footer.php';
    }

    // URL: /UnityExchange/product/update/5
    // Captures the form submission from edit() and updates the database
    public function update($id) {
        $this->requireLogin();

        // Security Check: Re-verify ownership before allowing the update
        $product = $this->productModel->getProductById($id);
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('HTTP/1.0 403 Forbidden');
            die("<h1>403 Forbidden</h1>");
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. CSRF Token Check
            $this->validateCRSF("/UnityExchange/product/edit/" . $id);

            // 2. Extract inputs
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
            $stock_quantity = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : 0;
            $seller_id = $_SESSION['user_id'];
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1; 

            // 3. Process the image upload (Passing 'false' makes it optional for updates)
            $uploadResult = $this->handleImageUpload($_FILES['product_image'] ?? null, false);
            
            // If the user uploaded a bad file (wrong type/too big), kick them back
            if (!$uploadResult['success']) {
                $_SESSION['flash_error'] = $uploadResult['error'];
                header("Location: /UnityExchange/product/edit/" . $id);
                exit();
            }

            // This will be a filename if they uploaded a new image, or NULL if they didn't
            $image_filename = $uploadResult['filename']; 

            // 4. Update the database record
            if ($this->productModel->updateProduct($id, $seller_id, $category_id, $name, $description, $price, $stock_quantity, $image_filename)) {
                $_SESSION['flash_message'] = "Product updated successfully!";
                $_SESSION['flash_type'] = "success";
                // Success! Send them to the product details page so they can see their updates
                header("Location: /UnityExchange/product/details/" . $id);
                exit();
            } else {
                $_SESSION['flash_message'] = "Database error: Could not update your product.";
                $_SESSION['flash_type'] = "error";
                header("Location: /UnityExchange/product/edit/" . $id);
                exit();
            }
        } else {
            // If not POST, redirect back to the edit page
            header("Location: /UnityExchange/product/edit/" . $id);
            exit();
        }
    }

    // URL: /UnityExchange/product/delete/5
    // Removes the product from the database
    public function delete($id) {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Strict CSRF verification for destructive actions
            $this->validateCRSF("/UnityExchange/product/edit/" . $id);

            $seller_id = $_SESSION['user_id'];

            // The model automatically enforces the WHERE user_id = :seller_id constraint
            if ($this->productModel->deleteProduct($id, $seller_id)) {
                $_SESSION['flash_message'] = "Product deleted successfully.";
                $_SESSION['flash_type'] = "success";
                header("Location: /UnityExchange/product/myListings");
                exit();
            } else {
                // PRG Pattern: Database failed (likely because the item is tied to an existing order)
                $_SESSION['flash_message'] = "Error: Cannot delete this product. It may already be linked to an active customer order.";
                $_SESSION['flash_type'] = "error";
                header("Location: /UnityExchange/product/edit/" . $id);
                exit();
            }
        } else {
            header("Location: /UnityExchange/home");
            exit();
        }
    }

    // URL: /UnityExchange/product/myListings 
    // Shows a private dashboard of all items listed by the logged-in user
    public function myListings() {
        $this->requireLogin();
        
        $user_id = $_SESSION['user_id'];
        
        // Fetch only products belonging to this specific user
        $products = $this->productModel->getProductsBySeller($user_id);
        $categories = $this->productModel->getCategories();

        require_once 'includes/header.php';
        require_once 'views/product/my_listings.php';
        require_once 'includes/footer.php';
    }
}