<div class="cart-section">
    
    <div class="cart-header">
        <div>
            <h1>Checkout Review</h1>
            <p>Please review your order details before confirming.</p>
        </div>
        <a href="/UnityExchange/cart" class="btn-primary">← Back to Cart</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="cart-layout">
        
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" style="cursor: default;">
                    <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($item['product']['image_file']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                        <p>Price: 
                            <strong style="color: #3182ce; font-weight: bold;">
                                R <?php echo number_format($item['product']['price'], 2); ?>
                            </strong>
                        </p>
                    </div>
                    
                    <div class="item-quantity">
                        <label>Quantity</label>
                        <p style="font-size: 1.1rem; font-weight: bold; color: #2d3748; margin: 0;">
                            <?php echo htmlspecialchars($item['quantity']); ?>
                        </p>
                    </div>
                    
                    <div class="item-actions">
                        <label style="font-size: 0.85rem; color: #718096; font-weight: 600;">Subtotal</label>
                        <p>R <?php echo number_format($item['subtotal'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <div>
                <span>Items</span>
                <span>(<?php echo count($cart_items); ?>)</span>
            </div>

            <div>
                <span>Estimated Tax/Fees</span>
                <span>Tax-Exempt</span>
            </div>
            
            <div>
                <span>Total</span>
                <span>R <?php echo number_format($grand_total, 2); ?></span>
            </div>

            <form action="/UnityExchange/checkout/process" method="POST" style="margin: 0;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn-primary">Confirm & Pay</button>
            </form>
            <p>By confirming, you agree to the platform's terms of service.</p>
        </div>

    </div>
</div>