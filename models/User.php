<?php
// models/User.php

class User {
    private $db;

    public function __construct($database_connection)
    {
        $this->db = $database_connection;
    }

    // --- AUTHENTICATION & REGISTRATION ---

    // Find the user by their email for logging in
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);

        // Returns the associative array or false if no user is found
        return $stmt->fetch();
    }

    // Insert a new user into the database for registration
    public function createUser($username, $email, $password_hash) {
        try {
            $query = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
            $stmt = $this->db->prepare($query);
        
            if ($stmt->execute([':username' => $username, ':email' => $email, ':password_hash' => $password_hash])) {
                // Return the ID of the newly created user
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
    }

    // --- ROLE MANAGEMENT ---

    // Fetch a flat array of role names for the session
    public function getUserRoles($user_id) {
        $query = "SELECT r.name FROM roles r JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = :user_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Get all available roles from the database for the admin interface
    public function getAllAvailableRoles() {
        $query = "SELECT * FROM roles";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Assign a role to a user by its name (used during registration)
    public function assignRoleByName($user_id, $role_name) {
        try {
            // Find the role by its name to ensure it exists
            $query = "SELECT id FROM roles WHERE name = :name LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':name' => $role_name]);
            $role = $stmt->fetch();

            if ($role) {
                $query = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
                $stmt = $this->db->prepare($query);
                return $stmt->execute([':user_id' => $user_id, ':role_id' => $role['id']]);
            } else {
                return false; // Role not found
            }
        } catch (PDOException $e) {
            error_log('Failed to assign role: ' . $e->getMessage());
            return false;
        }
    }

    // Update user roles (Used by Admin) using a database transaction to ensure data integrity
    public function updateUserRoles($user_id, $role_ids) {
        $this->db->beginTransaction();

        try {
            // Remove existing roles for the user
            $query = "DELETE FROM user_roles WHERE user_id = :user_id";
            $delStmt = $this->db->prepare($query);
            $delStmt->execute([':user_id' => $user_id]);

            // Insert the newly checked roles for the user
            if (!empty($role_ids)) {
                $insStmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
                foreach ($role_ids as $role_id) {
                    $insStmt->execute([':user_id' => $user_id, ':role_id' => $role_id]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // --- ADMIN CRUD OPERATIONS ---

    // Fetch all users and concatenate their roles into a comma-separated string for display in the admin panel
    public function getAllUsersWithRoles() {
        $stmt = $this->db->query("
            SELECT u.id, u.username, u.email, u.created_at, GROUP_CONCAT(r.name SEPARATOR ', ') AS roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id"
        );
        return $stmt->fetchAll();
    }
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, password_hash, profile_picture, description FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateUser($id, $username, $email, $description = null) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, description = :description WHERE id = :id");
            return $stmt->execute([':id' => $id, ':username' => $username, ':email' => $email, ':description' => $description]);
        } catch (PDOException $e) {
            error_log("User update failed: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $password_hash) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
            return $stmt->execute([':id' => $id, ':password_hash' => $password_hash]);

        } catch (PDOException $e) {
            error_log("Password update failed: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfilePicture($id, $fileName) {
        $query = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':profile_picture', $fileName);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("User delete failed: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}