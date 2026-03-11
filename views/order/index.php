<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Order History</h1>
            <p>Track your purchases and view your digital receipts.</p>
        </div>
        <a href="/UnityExchange/product" class="btn-primary">← Back to Marketplace</a>
    </div>

    <?php if (empty($orders)): ?>
        
        <div class="empty-state">
            <h3>No orders yet</h3>
            <p>You haven't purchased anything from the marketplace yet.</p>
            <a href="/UnityExchange/product" class="btn-primary" style="display: inline-block; margin-top: 10px;">
                Start Exploring
            </a>
        </div>

    <?php else: ?>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($orders as $order): ?>
                
                <div class="product-card" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 25px; flex-direction: row; gap: 15px;">

                    <?php 
                        $status = strtolower($order['status']);
                        if ($status === 'completed') {
                            $badgeClass = 'stock-badge in-stock'; 
                            $statusText = 'Completed';
                            $inlineStyle = '';
                        } elseif ($status === 'cancelled') {
                            $badgeClass = 'stock-badge sold-out'; 
                            $statusText = 'Cancelled';
                            $inlineStyle = '';
                        } else {
                            // Default Pending State
                            $badgeClass = 'stock-badge';
                            $statusText = 'Pending';
                            // Removed align-self from here so the parent h3 controls it uniformly
                            $inlineStyle = 'background-color: #ebf8ff; color: #2b6cb0;'; 
                        }
                    ?>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <h3 style="margin-top: 0; margin-bottom: 5px; color: #2b6349; font-size: 1.25rem; display: flex; align-items: flex-start; gap: 12px;">
                            Order Reference #<?php echo htmlspecialchars($order['id']); ?> 
                            
                            <span class="<?php echo $badgeClass; ?>" style="<?php echo $inlineStyle; ?> margin-top: 2px;">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </h3>
                        
                        <p style="margin: 0 0 8px 0; color: #718096; font-size: 0.95rem;">
                            Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </p>
                        <span style="font-size: 1.25rem; font-weight: bold; color: #3182ce;">
                            R <?php echo number_format($order['grand_total'] ?? $order['total_amount'], 2); ?>
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap; justify-content: flex-end;">

                        <a href="/UnityExchange/order/details/<?php echo $order['id']; ?>" class="btn-primary" style="padding: 10px 24px; font-size: 0.95rem; margin: 0; white-space: nowrap;
                            background: linear-gradient(135deg, #2f855a 0%, #22543d 100%);">
                            View Details
                        </a>
                        
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>