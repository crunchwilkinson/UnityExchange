<?php
// models/Order.php

class Order {
    private $db;

    public function __construct($database_connection)
    {
        $this->db = $database_connection;
    }

    // Process the entire order in one single safe transaction
    public function createOrder($user_id, $cart_items, $grand_total) {
        try {
            // Start a transaction to ensure all operations succeed or fail together
            $this->db->beginTransaction();
    
            $query = "INSERT INTO  orders (user_id, total_amount, status) VALUES (:user_id, :total_amount, 'pending')";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => $user_id, ':total_amount' => $grand_total]);

            // Grab the newly created Order ID so we can attach the items to it
            $order_id = $this->db->lastInsertId();

            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
            VALUES (:order_id, :product_id, :quantity, :price_at_purchase)";

            $item_stmt = $this->db->prepare($item_query);

            $stock_query = "UPDATE products SET stock_quantity = stock_quantity - :quantity 
            WHERE id = :product_id AND stock_quantity >= :quantity";

            $stock_stmt = $this->db->prepare($stock_query);

            foreach ($cart_items as $item) {
                $product_id = $item['product']['id'];
                $quantity = $item['quantity'];
                $price = $item['product']['price'];

                // Insert each item into the order_items table
                $item_stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $product_id,
                    ':quantity' => $quantity,
                    ':price_at_purchase' => $price
                ]);

                //Deduct the stock quantity for each product
                $stock_stmt->execute([
                    ':quantity' => $quantity,
                    ':product_id' => $product_id
                ]);

                // Failsafe: If execute() didn't update any rows, it means the stock was insufficient for that product
                if ($stock_stmt->rowCount() === 0) {
                    throw new Exception("Insufficient stock for product ID: " . $product_id);
                }
            }

            // If we reach this point, it means all operations were successful, so we can commit the transaction
            $this->db->commit();
            return $order_id;

        } catch (PDOException $e) {
            // Handle any errors that occur during the transaction
            $this->db->rollBack();
                error_log("Order creation failed: " . $e->getMessage());
                return false;
        }
    }

    // Fetches all top-level order receipts for a specific buyer
    public function getOrdersByUserId($user_id) {
        $query = "SELECT id, total_amount, status, created_at 
                  FROM orders 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Fetch a single order, strictly checking the user_id for security
    public function getOrderById($order_id, $user_id) {
        $query = "SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
        
        return $stmt->fetch(); // Returns false if not found or doesn't belong to them
    }

    // Fetch all line items for an order, including the snapshot price and the seller's username
    public function getOrderItems($order_id) {
        $query = "SELECT oi.quantity, oi.price_at_purchase, 
                         p.name as product_name, p.image_file, p.id as product_id, 
                         u.username as seller_name
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  JOIN users u ON p.user_id = u.id
                  WHERE oi.order_id = :order_id";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute([':order_id' => $order_id]);
        
        return $stmt->fetchAll();
    }

    // Updates the order status to completed, strictly verifying ownership and current status
    public function markOrderCompleted($order_id, $user_id) {
        try {
            $query = "UPDATE orders 
                  SET status = 'completed' 
                  WHERE id = :order_id 
                  AND user_id = :user_id 
                  AND status = 'pending'";
                  
            $stmt = $this->db->prepare($query);
            $stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
        
            if ($stmt->rowCount() > 0) {
                return true; // Successfully updated
            } else {
                return false; // No rows updated, either because the order doesn't exist, doesn't belong to the user, or isn't pending
            }
        } catch (PDOException $e) {
            error_log("Failed to mark order as completed: " . $e->getMessage());
        }
    }

    // Fetch all orders that a specific seller has sold (for their own sales dashboard)
    public function getSalesBySellerId($seller_id) {
        $query = "SELECT oi.quantity, oi.price_at_purchase, 
                         p.name as product_name, p.image_file, 
                         o.id as order_id, o.created_at as sale_date, o.status as order_status,
                         u.username as buyer_name, u.email as buyer_email, u.id as buyer_id
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  JOIN orders o ON oi.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE p.user_id = :seller_id
                  ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':seller_id' => $seller_id]);

        return $stmt->fetchAll();
    }

    // Admin function to fetch all orders across the platform (for admin dashboard)
    public function getAllOrders() {
        $query = "SELECT o.id, o.total_amount, o.status, o.created_at,
                         u.username as buyer_name
                  FROM orders o
                  JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Force update on order's status (for admin use only, with strict checks in the controller)
    public function adminUpdateOrderStatus($order_id, $new_status) {
        try {
            $query = "UPDATE orders set status = :new_status WHERE id = :order_id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':new_status' => $new_status, ':order_id' => $order_id]);
        } catch (PDOException $e) {
            error_log("Admin failed to update order status: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalCompletedOrders() {
        $query = "SELECT COUNT(*) as total FROM orders WHERE status = 'completed'";
        $stmt = $this->db->query($query);
        $result = $stmt->fetch();
        return $result ? $result['total'] : 0;
    }
}