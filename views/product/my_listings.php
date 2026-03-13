<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>My Listings</h1>
            <p>Manage your items listed on the marketplace.</p>
        </div>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="/UnityExchange/product/create" class="btn-secondary">+ List an Item</a>
        <?php else: ?>
            <a href="/UnityExchange/auth/login" class="btn-secondary">Login to Sell</a>
        <?php endif; ?>
    </div>

    <div class="product-grid">
    
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            
            <div class="product-card">
                
                <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>" class="product-image-link">
                    <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                </a>

                <div class="product-card-body">
                    
                    <h3 class="product-title">
                        <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h3>
                    
                    <p class="product-price">
                        R <?php echo number_format($product['price'], 2); ?>
                    </p>

                    <div class="product-footer">
                        <span class="seller-info">
                            Sold by <strong><?php echo htmlspecialchars($product['seller_name']); ?></strong>
                        </span>
                        
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <span class="stock-badge in-stock">In Stock</span>
                        <?php else: ?>
                            <span class="stock-badge sold-out">Sold Out</span>
                        <?php endif; ?>
                    </div>
                    
                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        
        <div class="empty-state">
            <h3>No items found</h3>
            <p>You currently have no items listed.</p>
            <?php if (isset($_SESSION['logged_in'])): ?>
                <a href="/UnityExchange/product/create" class="btn-primary">List an Item Now</a>
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
    
    </div>
</div>