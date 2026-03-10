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
    
}