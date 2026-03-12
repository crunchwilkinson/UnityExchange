<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>Order #<?php echo htmlspecialchars($order['id']); ?></h1>
            <p>Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <a href="/UnityExchange/order" class="btn-primary">← Back to Order History</a>
    </div>

    <div class="cart-layout">
        
        <div class="cart-items">
            
            <?php foreach ($items as $item): ?>
                <div class="cart-item" style="cursor: default;">

                    <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($item['image_file']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">

                    <div class="item-details">
                        <h3>
                            <a href="/UnityExchange/product/details/<?php echo $item['product_id']; ?>">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </a>
                        </h3>
                        <p>Sold by: <strong><?php echo htmlspecialchars($item['seller_name']); ?></strong></p>
                        
                        <p style="color: #3182ce; font-weight: bold; margin-top: 5px;">
                            R <?php echo number_format($item['price_at_purchase'], 2); ?> each
                        </p>
                    </div>
                    
                    <div class="item-quantity">
                        <label>Purchased</label>
                        <p style="font-size: 1.1rem; font-weight: bold; color: #2d3748; margin: 0;">
                            <?php echo htmlspecialchars($item['quantity']); ?>
                        </p>
                    </div>
                    
                    <div class="item-actions">
                        <label style="font-size: 0.85rem; color: #718096; font-weight: 600;">Subtotal</label>
                        <p style="margin: 0;">
                            R <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary" style="height: fit-content;">
            <h2>Receipt Summary</h2>
            
            <div>
                <span>Status</span>
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
                        $badgeClass = 'stock-badge';
                        $statusText = 'Pending';
                        $inlineStyle = 'background-color: #ebf8ff; color: #2b6cb0;'; 
                    }
                ?>
                <span class="<?php echo $badgeClass; ?>" style="<?php echo $inlineStyle; ?> font-size: 0.95rem;">
                    <?php echo htmlspecialchars($statusText); ?>
                </span>
            </div>

            <div>
                <span>Items Subtotal</span>
                <span>R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            
            <div style="border-top: 2px solid #edf2f7; padding-top: 15px; margin-top: 15px;">
                <span style="font-size: 1.25rem; font-weight: bold; color: #2d3748;">Total</span>
                <span style="font-size: 1.25rem; font-weight: bold; color: #3182ce;">R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>

            <?php if ($status === 'pending'): ?>
                    <form action="/UnityExchange/order/complete/<?php echo $order['id']; ?>" method="POST" style="margin: 0;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #38a169 0%, #2f855a 100%); width: 100%;">
                            Mark as Received
                        </button>
                    </form>
                    <p style="font-size: 0.85rem; color: #718096; margin-bottom: 15px;">
                        Have you received these items from the seller?
                    </p>
                
            <?php endif; ?>

        </div>
        
    </div>
</div>