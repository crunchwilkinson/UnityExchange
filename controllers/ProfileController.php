<?php
// controllers/ProfileController.php

require_once 'config/Database.php';
require_once 'models/User.php';

class ProfileController {
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connect();

        $this->userModel = new User($db);
    }


    // ==================================
    // PRIVATE HELPER METHODS
    // ==================================

   private function requireLogin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $_SESSION['flash_error'] = "Please log in to view your profile.";
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

    // Reusing your optimal upload logic from ProductController
    private function handleImageUpload($fileArray) {
        // 1. If no file was uploaded, that's fine for profiles (it's optional)
        if (!isset($fileArray) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'filename' => null]; 
        }

        // 2. Check for other network/server upload errors
        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => "A network error occurred during file upload."];
        }
        
        // 3. Security: Enforce a strict 5MB file size limit
        if ($fileArray['size'] > 5000000) {
            return ['success' => false, 'error' => "File size exceeds the 5MB limit. Please choose a smaller image."];
        }

        // 4. Security: Verify the actual MIME type
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array(mime_content_type($fileArray['tmp_name']), $allowed_mime_types)) {
            return ['success' => false, 'error' => "Invalid file type. Only JPEG, PNG, and WEBP images are allowed."];
        }

        // 5. Generate unique filename
        $file_extension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
        
        // Define where the file should be saved (Note the /users/ directory)
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/UnityExchange/assets/images/users/' . $unique_filename;

        // Ensure directory exists
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        // 6. Move the file
        if (move_uploaded_file($fileArray['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $unique_filename];
        }

        return ['success' => false, 'error' => "Server error: Failed to save the uploaded image to the directory."];
    }

    public function index() {
        $this->requireLogin();

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            // Failsafe guard: if user not found, log out
            header('Location: /UnityExchange/auth/logout');
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/profile/index.php';
        require_once 'includes/footer.php';
    }

    public function updateDetails() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /UnityExchange/profile");
            exit();
        }

        // Validate CSRF
        $this->validateCRSF("/UnityExchange/profile");

        $user_id = $_SESSION['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // 1. Process the profile picture upload using the optimal helper method
        $uploadResult = $this->handleImageUpload($_FILES['profile_picture'] ?? null);
            
        // If the upload failed (wrong type, too big), kick them back with the error
        if (!$uploadResult['success']) {
            $_SESSION['flash_message'] = $uploadResult['error'];
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }

        // 2. If an image was successfully uploaded, update the database with the new filename
        if ($uploadResult['filename'] !== null) {
            $this->userModel->updateProfilePicture($user_id, $uploadResult['filename']);
            // Optionally: Update the session variable so the navbar reflects the new image instantly
            $_SESSION['profile_picture'] = $uploadResult['filename'];
        }

        // 3. Update the basic text info
        $this->userModel->updateUser($user_id, $username, $email);
        $_SESSION['username'] = $username; // Update the active session username

        $_SESSION['flash_success'] = "Account details updated successfully!";
        $_SESSION['flash_type'] = "success";
        header("Location: /UnityExchange/profile");
        exit();
    }
}