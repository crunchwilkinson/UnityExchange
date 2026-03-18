<div class="cart-section">
    
    <div class="cart-header">
        <div>
            <h1>Your Cart</h1>
            <p>Review your items before proceeding to checkout.</p>
        </div>
        <a href="/UnityExchange/product" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Continue Shopping
        </a>
    </div>

    <?php if (empty($cart_items)): ?>
        
        <div class="empty-cart">
            <h2>Your cart is currently empty.</h2>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="/UnityExchange/product" class="btn-primary">Discover Marketplace Items</a>
        </div>

    <?php else: ?>

        <div class="cart-layout">
            
            <div class="cart-items">
                
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        
                        <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($item['product']['image_file']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                        
                        <div class="item-details">
                            <h3>
                                <a href="/UnityExchange/product/details/<?php echo $item['product']['id']; ?>">
                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                </a>
                            </h3>
                            <p>
                                Sold by: <strong><?php echo htmlspecialchars($item['product']['seller_name']); ?></strong>
                            </p>
                            <p style="color: #3182ce; font-weight: bold;">
                                R <?php echo number_format($item['product']['price'], 2); ?> each
                            </p>
                        </div>

                        <div class="item-quantity">
                            <label for="qty-<?php echo $item['product']['id']; ?>">Qty</label>
                            
                            <input type="number" 
                                   class="cart-qty-input" 
                                   id="qty-<?php echo $item['product']['id']; ?>" 
                                   data-id="<?php echo $item['product']['id']; ?>" 
                                   value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                   min="1" 
                                   max="<?php echo htmlspecialchars($item['product']['stock_quantity']); ?>">
                            
                            <p>
                                <?php echo htmlspecialchars($item['product']['stock_quantity']); ?> max
                            </p>
                        </div>

                        <div class="item-actions">
                            <p>
                                R <?php echo number_format($item['subtotal'], 2); ?>
                            </p>
                            
                            <button class="remove-item-btn" data-id="<?php echo $item['product']['id']; ?>">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="text-align: left; margin-top: 15px;">
                    <button id="clear-cart-btn">
                        Empty entire cart
                    </button>
                </div>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div>
                    <span>Subtotal</span>
                    <span>R <?php echo number_format($grand_total, 2); ?></span>
                </div>
                
                <div>
                    <span>Estimated Tax/Fees</span>
                    <span>Calculated at checkout</span>
                </div>

                <div>
                    <span>Total</span>
                    <span>R <?php echo number_format($grand_total, 2); ?></span>
                </div>

                <a href="/UnityExchange/checkout" class="btn-primary">
                    Proceed to Checkout
                </a>
                
                <p>
                    Secure transaction via UnityExchange
                </p>
            </div>
            
        </div>
    <?php endif; ?>
</div>