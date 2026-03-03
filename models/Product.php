<?php
// models/Product.php

class Product
{
    private $db;

    // The constructor accepts a database connection, which will be used for all database operations in this model
    public function __construct($database_connection)
    {
        $this->db = $database_connection;
    }

    // ==========================================
    // CATEGORY OPERATIONS
    // ==========================================

    // Fetch all available categories for the dropdown menus
    public function getCategories()
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // READ OPERATIONS
    // ==========================================

    // Fetch products directly from MySQL
    public function getAllProducts()
    {
        $query = "SELECT p.*, u.username as seller_name, c.name as category_name
                  FROM products p
                  JOIN users u ON p.user_id = u.id
                  JOIN categories c ON p.category_id = c.id
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        $query = "SELECT p.*, u.username as seller_name, u.email as seller_email, c.name as category_name   
        FROM products p
        JOIN users u ON p.user_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductsBySeller($user_id)
    {
        $query = "SELECT p.*, u.username as seller_name, c.name as category_name 
                  FROM products p
                  JOIN users u ON p.user_id = u.id
                  JOIN categories c ON p.category_id = c.id
                  WHERE p.user_id = :user_id 
                  ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // WRITE OPERATIONS
    // ==========================================

    public function createProduct($user_id, $category_id, $name, $description, $image_file, $price, $stock_quantity)
    {
        try {
            $query = "INSERT INTO products (user_id, category_id, name, description, image_file, price, stock_quantity)
                      VALUES (:user_id, :category_id, :name, :description, :image_file, :price, :stock_quantity)";

            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ":user_id" => $user_id,
                ":category_id" => $category_id,
                ":name" => $name,
                ":description" => $description,
                ":image_file" => $image_file,
                ":price" => $price,
                ":stock_quantity" => $stock_quantity
            ]);
        } catch (PDOException $e) {
            error_log("Failed to create product: " . $e->getMessage());
            return false;
        }
    }



    public function updateProduct($id, $user_id, $category_id, $name, $description, $price, $stock_quantity, $image_file = null)
    {
        try {
            if ($image_file) {
                $query = "UPDATE products 
                          SET category_id = :category_id, name = :name, description = :description, 
                              image_file = :image_file, price = :price, stock_quantity = :stock_quantity 
                          WHERE id = :id AND user_id = :user_id";

                $params = [
                    ":id" => $id,
                    ":user_id" => $user_id,
                    ":category_id" => $category_id,
                    ":name" => $name,
                    ":description" => $description,
                    ":image_file" => $image_file,
                    ":price" => $price,
                    ":stock_quantity" => $stock_quantity
                ];
            } else {
                $query = "UPDATE products 
                          SET category_id = :category_id, name = :name, description = :description, 
                              price = :price, stock_quantity = :stock_quantity 
                          WHERE id = :id AND user_id = :user_id";

                $params = [
                    ":id" => $id,
                    ":user_id" => $user_id,
                    ":category_id" => $category_id,
                    ":name" => $name,
                    ":description" => $description,
                    ":price" => $price,
                    ":stock_quantity" => $stock_quantity
                ];
            }

            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Failed to update product: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id, $user_id)
    {
        try {
            $query = "DELETE FROM products WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Failed to delete product: " . $e->getMessage());
            return false;
        }
    }

    public function adminDeleteProduct($id)
    {
        try {
            $query = "DELETE FROM products WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Failed to delete product: " . $e->getMessage());
            return false;
        }
    }
}
