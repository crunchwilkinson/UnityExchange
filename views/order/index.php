<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Order History</h1>
            <p>Track your purchases and view your digital receipts.</p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/product/" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Go Back
        </a>
    </div>

    <?php if (empty($orders)): ?>
        
        <div class="empty-cart">
            <h2>No orders yet</h2>
            <p>You haven't purchased anything from the marketplace yet.</p>
            <a href="<?php echo $_ENV['APP_URL']; ?>/product" class="btn-primary btn-sm">
                Start Exploring
            </a>
        </div>

    <?php else: ?>

        <div class="dashboard-list">
            <?php foreach ($orders as $order): ?>
                
                <div class="product-card order-history-card">

                    <?php 
                        // Simplified Badge Logic using our existing CSS states
                        $status = strtolower($order['status']);
                        if ($status === 'completed') {
                            $badgeClass = 'stock-badge in-stock'; 
                            $statusText = 'Completed';
                        } elseif ($status === 'cancelled') {
                            $badgeClass = 'stock-badge sold-out'; 
                            $statusText = 'Cancelled';
                        } else {
                            // Uses the .pending class we made earlier!
                            $badgeClass = 'stock-badge pending';
                            $statusText = 'Pending';
                        }
                    ?>
                    
                    <div class="order-history-details">
                        <h3 class="order-ref-title">
                            Order Reference #<?php echo htmlspecialchars($order['id']); ?> 
                            
                            <span class="<?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </h3>
                        
                        <p class="order-meta-text">
                            Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </p>
                        <span class="order-total-price">
                            R <?php echo number_format($order['grand_total'] ?? $order['total_amount'], 2); ?>
                        </span>
                    </div>

                    <div class="order-history-actions">
                        <a href="<?php echo $_ENV['APP_URL']; ?>/order/details/<?php echo $order['id']; ?>" class="btn-primary btn-sm btn-green no-margin">
                            View Details
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>