<?php
// controllers/ProfileController.php

require_once 'BaseController.php';
require_once 'models/User.php';

class ProfileController extends BaseController {
    private $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->userModel = new User($this->db);
    }


    // ==================================
    // PRIVATE HELPER METHODS
    // ==================================

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
        $this->validateCSRF("/UnityExchange/profile");

        $user_id = $_SESSION['user_id'];
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : null;

        if (empty($username) || empty($email)) {
            $_SESSION['flash_message'] = "Please fill in all required fields.";
            header("Location: /UnityExchange/profile");
            exit();
        }

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
        if($this->userModel->updateUser($user_id, $username, $email, $description)) {
            $_SESSION['username'] = $username; // Update the active session username

            $_SESSION['flash_message'] = "Account details updated successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: /UnityExchange/profile");
            exit();
        } else {
            $_SESSION['flash_message'] = "Failed to update account details. Please try again.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }
    }

    public function updatePassword() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /UnityExchange/profile");
            exit();
        }

        // Validate CSRF
        $this->validateCSRF("/UnityExchange/profile");

        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['flash_message'] = "Please fill in all password fields.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['flash_message'] = "New password and confirmation do not match.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }

        $user = $this->userModel->getUserById($user_id);

        if (empty($user)) {
            $_SESSION['flash_message'] = "User not found.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }

        if (!password_verify($current_password, $user['password_hash'])) {
            $_SESSION['flash_message'] = "Please ensure your current password is correct.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }

        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        if ($this->userModel->updatePassword($user_id, $new_password_hash)) {
            $_SESSION['flash_message'] = "Password updated successfully!";
            $_SESSION['flash_type'] = "success";
            header("Location: /UnityExchange/profile");
            exit();
        } else {
            $_SESSION['flash_message'] = "Failed to update password. Please try again.";
            $_SESSION['flash_type'] = "error";
            header("Location: /UnityExchange/profile");
            exit();
        }
    }

    public function details($id) {
        // Fetch the user's data
        $user = $this->userModel->getUserById($id);

        if (!$user) {
            echo "<h1>404 Error</h1><p>User profile not found.</p>";
            exit();
        }

        require_once 'includes/header.php';
        require_once 'views/profile/details.php';
        require_once 'includes/footer.php';
    }
}