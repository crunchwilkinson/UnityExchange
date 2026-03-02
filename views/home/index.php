<main class="products-section">
    <div class="catalog-header">
        <div>
            <h1>Latest Products</h1>
            <p>Discover the newest items from sellers in your community.</p>
        </div>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="/UnityExchange/product/create" class="btn-primary">+ List an Item</a>
        <?php else: ?>
            <a href="/UnityExchange/auth/login" class="btn-primary">Login to Sell</a>
        <?php endif; ?>
    </div>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                
                <div class="product-card">
                    
                    <a href="/UnityExchange/product/show/<?php echo $product['id']; ?>" class="product-image-link">
                        <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-image">
                    </a>

                    <div class="product-card-body">
                        
                        <h3 class="product-title">
                            <a href="/UnityExchange/product/show/<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        
                        <p class="product-price">
                            R <?php echo number_format($product['price'], 2); ?>
                        </p>

                        <?php if (!empty($product['description'])): ?>
                            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 15px; line-height: 1.5;">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>
                                <?php if (strlen($product['description']) > 80) echo '...'; ?>
                            </p>
                        <?php endif; ?>

                        <div class="product-footer">
                            <?php if (!empty($product['seller_name'])): ?>
                                <span class="seller-info">
                                    By <strong><?php echo htmlspecialchars($product['seller_name']); ?></strong>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (isset($product['stock_quantity'])): ?>
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span class="stock-badge in-stock">In Stock</span>
                                <?php else: ?>
                                    <span class="stock-badge sold-out">Sold Out</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            
            <div class="empty-state">
                <h3>No products yet</h3>
                <p>Be the first to list an item on UnityExchange!</p>
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <a href="/UnityExchange/product/create" class="btn-primary">List an Item Now</a>
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
    </div>
</main>