<div class="cart-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Sales Dashboard</h1>
            <p>Track the items you've sold and arrange fulfillment with buyers.</p>
        </div>
        <a href="/UnityExchange/product/create" class="btn-primary">List New Product</a>
    </div>

    <?php if (empty($sales)): ?>
        
        <div class="empty-state">
            <h3>No sales yet</h3>
            <p>Keep listing great products! Your sales will appear here.</p>
        </div>

    <?php else: ?>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($sales as $sale): ?>
                
                <?php 
                    // Reusing your badge logic for the order status
                    $status = strtolower($sale['order_status']);
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
                        $statusText = 'Pending Delivery';
                        $inlineStyle = 'background-color: #ebf8ff; color: #2b6cb0;'; 
                    }
                ?>

                <div class="product-card" style="display: flex; flex-direction: column; padding: 25px; gap: 20px;">
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: space-between;">
                        
                        <div style="display: flex; gap: 15px; flex: 1; min-width: 250px;">
                            <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($sale['image_file']); ?>" 
                                 alt="<?php echo htmlspecialchars($sale['product_name']); ?>" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #edf2f7;">
                            
                            <div>
                                <h3 style="margin-top: 0; margin-bottom: 5px; color: #2d3748; font-size: 1.25rem;">
                                    <?php echo htmlspecialchars($sale['product_name']); ?>
                                </h3>
                                <p style="margin: 0; color: #718096; font-size: 0.95rem;">
                                    Sold on <?php echo date('F j, Y', strtotime($sale['sale_date'])); ?> <br>
                                    (Order #<?php echo htmlspecialchars($sale['order_id']); ?>)
                                </p>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <span class="<?php echo $badgeClass; ?>" style="<?php echo $inlineStyle; ?> display: inline-block; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>
                        </div>
                    </div>

                    <div style="background-color: #f7fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 0.85rem; color: #3182ce; font-weight: bold; text-transform: uppercase;">Quantity Sold:</span>
                            <span style="font-size: 0.85rem; color: #3182ce; font-weight: bold;">
                                <?php echo htmlspecialchars($sale['quantity']); ?>
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 8px; text-align: right;">
                            <span style="font-size: 0.85rem; color: #718096; font-weight: bold; text-transform: uppercase;">Total Earned:</span>
                            <span style="font-size: 1.25rem; font-weight: bold; color: #38a169;">
                                R <?php echo number_format($sale['price_at_purchase'] * $sale['quantity'], 2); ?>
                            </span>
                        </div>

                    </div>

                    <div style="border-top: 1px solid #edf2f7; padding-top: 15px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 1rem; color: #4a5568;">Buyer Details</h4>
                        <p style="margin: 0 0 5px 0; color: #2d3748;">
                            <strong>Username:</strong> <?php echo htmlspecialchars($sale['buyer_name']); ?>
                        </p>
                        <p style="margin: 0; color: #2d3748;">
                            <strong>Email:</strong> 
                            <a href="mailto:<?php echo htmlspecialchars($sale['buyer_email']); ?>" style="color: #3182ce; text-decoration: none;">
                                <?php echo htmlspecialchars($sale['buyer_email']); ?>
                            </a>
                        </p>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>