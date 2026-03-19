<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Sales Dashboard</h1>
            <p>Track the items you've sold and arrange fulfillment with buyers.</p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/product" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Back to Marketplace
        </a>
    </div>

    <?php if (empty($sales)): ?>
        
        <div class="empty-cart">
            <h2>No sales yet</h2>
            <p>Keep listing great products! Your sales will appear here.</p>
            <a href="<?php echo $_ENV['APP_URL']; ?>/product/create" class="btn-primary" style="display: inline-block; margin-top: 10px;">
                List New Product
            </a>
        </div>

    <?php else: ?>

        <div class="dashboard-list">
            <?php foreach ($sales as $sale): ?>
                
                <?php 
                    // Simplified Badge Logic (Removed $inlineStyle)
                    $status = strtolower($sale['order_status']);
                    if ($status === 'completed') {
                        $badgeClass = 'stock-badge in-stock'; 
                        $statusText = 'Completed';
                    } elseif ($status === 'cancelled') {
                        $badgeClass = 'stock-badge sold-out'; 
                        $statusText = 'Cancelled';
                    } else {
                        // Utilizes the new .pending CSS class
                        $badgeClass = 'stock-badge pending';
                        $statusText = 'Pending Delivery';
                    }
                ?>

                <div class="product-card order-card">
                    
                    <div class="order-header">
                        
                        <div class="order-product-info">
                            <img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/products/<?php echo htmlspecialchars($sale['image_file']); ?>" 
                                 alt="<?php echo htmlspecialchars($sale['product_name']); ?>" 
                                 class="order-product-image">
                            
                            <div>
                                <h3 class="order-product-title">
                                    <?php echo htmlspecialchars($sale['product_name']); ?>
                                </h3>
                                <p class="order-meta-text">
                                    Sold on <?php echo date('F j, Y', strtotime($sale['sale_date'])); ?> <br>
                                    (Order #<?php echo htmlspecialchars($sale['order_id']); ?>)
                                </p>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <span class="<?php echo $badgeClass; ?>" style="display: inline-block; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </div>
                    </div>

                    <div class="financial-summary-box">
                        
                        <div class="financial-detail">
                            <span class="financial-label">Quantity Sold:</span>
                            <span class="financial-value">
                                <?php echo htmlspecialchars($sale['quantity']); ?>
                            </span>
                        </div>
                        
                        <div class="financial-detail align-right">
                            <span class="financial-label-alt">Total Earned:</span>
                            <span class="financial-total">
                                R <?php echo number_format($sale['price_at_purchase'] * $sale['quantity'], 2); ?>
                            </span>
                        </div>

                    </div>

                    <div class="user-details-section">
                        <h4 class="user-details-title">Buyer Details</h4>
                        <p class="user-details-text">
                            <strong>Username: </strong> 
                            
                                <a href="<?php echo $_ENV['APP_URL']; ?>/profile/details/<?php echo $sale['buyer_id']; ?>" class="seller-link">
                                    <?php echo htmlspecialchars($sale['buyer_name']); ?>
                                </a>
                            
                        </p>
                        <p class="user-details-text">
                            <strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($sale['buyer_email']); ?>" class="user-details-link">
                                <?php echo htmlspecialchars($sale['buyer_email']); ?>
                            </a>
                        </p>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>