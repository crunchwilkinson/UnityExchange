<?php
// models/Product.php

class Product {
    private $db;

    // The constructor accepts a database connection, which will be used for all database operations in this model
    public function __construct($database_connection) {
     $this->db = $database_connection;
    }
    
    // Fetch products directly from MySQL
    public function getAllProducts() {
        // Prepare the statement
        $query = "SELECT p.*, u.username as seller_name 
        FROM products p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
        
        // Prepare the statement
        $stmt = $this->db->prepare($query);
        
        // Execute the query
        $stmt->execute();

        // Return the results
        return $stmt->fetchAll();
    }

    public function getProductById($id) {
        $query = "SELECT p.*, u.username as seller_name, u.email as seller_email
        FROM products p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getProductsBySeller($user_id) {
        $query = "SELECT * FROM products WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function createProduct($user_id, $name, $description, $image_file, $price, $stock_quantity) {
        try {
            $query = "INSERT INTO products (user_id, name, description, image_file, price, stock_quantity)
            VALUES (:user_id, :name, :description, :image_file, :price, :stock_quantity)";

            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ":user_id"=> $user_id,
                ":name" => $name,
                ":description" => $description,
                ":image_file" => $image_file,
                ":price" => $price,
                ":stock_quantity" => $stock_quantity
            ]);
        } catch (PDOException $e) {
            echo "Failed to create product: " . $e->getMessage();
            return false;
        }
    }

    public function updateProduct($user_id, $name, $description, $image_file = null, $price, $stock_quantity) {
        try {
            if ($image_file) {
                $query = "UPDATE products 
                SET name = :name, description = :description, image_file = :image_file, price = :price, stock_quantity = :stock_quantity 
                WHERE user_id = :user_id";

                $params = [
                    ":user_id"=> $user_id,
                    ":name" => $name,
                    ":description" => $description,
                    ":image_file" => $image_file,
                    ":price" => $price,
                    ":stock_quantity" => $stock_quantity
                ];
            }
            else {
                $query = "UPDATE products 
                SET name = :name, description = :description, price = :price, stock_quantity = :stock_quantity 
                WHERE user_id = :user_id";

                $params = [
                    ":user_id"=> $user_id,
                    ":name" => $name,
                    ":description" => $description,
                    ":price" => $price,
                    ":stock_quantity" => $stock_quantity
                ];
            }

            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            echo "Failed to update product: " . $e->getMessage();
            return false;
        }
    }

    public function deleteProduct($id, $user_id) {
        try {
            $query = "DELETE FROM products WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        } catch (PDOException $e) {
            echo "Failed to delete product: " . $e->getMessage();
            return false;
        }
    }
        
}