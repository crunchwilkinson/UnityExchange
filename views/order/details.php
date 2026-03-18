<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>Order #<?php echo htmlspecialchars($order['id']); ?></h1>
            <p>Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <a href="/UnityExchange/order" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Back to Order History
        </a>
    </div>

    <div class="cart-layout">
        
        <div class="cart-items">
            
            <?php foreach ($items as $item): ?>
                <div class="cart-item cart-item-readonly">

                    <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($item['image_file']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">

                    <div class="item-details">
                        <h3>
                            <a href="/UnityExchange/product/details/<?php echo $item['product_id']; ?>">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </a>
                        </h3>
                        <p>Sold by: <strong><?php echo htmlspecialchars($item['seller_name']); ?></strong></p>
                        
                        <p class="item-price-each">
                            R <?php echo number_format($item['price_at_purchase'], 2); ?> each
                        </p>
                    </div>
                    
                    <div class="item-quantity">
                        <label>Purchased</label>
                        <p class="qty-display-value">
                            <?php echo htmlspecialchars($item['quantity']); ?>
                        </p>
                    </div>
                    
                    <div class="item-actions">
                        <label class="action-label-small">Subtotal</label>
                        <p class="no-margin">
                            R <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary">
            <h2>Receipt Summary</h2>
            
            <div>
                <span>Status</span>
                <?php 
                    // Cleaned up badge logic utilizing the .pending class
                    $status = strtolower($order['status']);
                    if ($status === 'completed') {
                        $badgeClass = 'stock-badge in-stock'; 
                        $statusText = 'Completed';
                    } elseif ($status === 'cancelled') {
                        $badgeClass = 'stock-badge sold-out'; 
                        $statusText = 'Cancelled';
                    } else {
                        $badgeClass = 'stock-badge pending';
                        $statusText = 'Pending';
                    }
                ?>
                <span class="<?php echo $badgeClass; ?> badge-md">
                    <?php echo htmlspecialchars($statusText); ?>
                </span>
            </div>

            <div>
                <span>Items Subtotal</span>
                <span>R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            
            <div>
                <span>Total</span>
                <span>R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>

            <?php if ($status === 'pending'): ?>
                    <form action="/UnityExchange/order/complete/<?php echo $order['id']; ?>" method="POST" class="no-margin-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        
                        <button type="submit" class="btn-primary btn-green">
                            Mark as Received
                        </button>
                    </form>
                    <p class="receipt-help-text">
                        Have you received these items from the seller?
                    </p>
                
            <?php endif; ?>

        </div>
        
    </div>
</div>