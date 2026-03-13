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

    <div style="background-color: #2c3e50; padding: 20px 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        
        <div style="color: #fff; font-size: 1.2rem; font-weight: 600;">
            <i class="fa fa-filter" style="color: #3498db; margin-right: 8px;"></i> Browse by Category
        </div>

        <div style="display: flex; align-items: center; gap: 15px;">
            <select id="categoryFilter" style="padding: 10px 15px; border: 1px solid #34495e; border-radius: 6px; background-color: #2c3e50; color: #fff; font-size: 0.95rem; min-width: 250px; cursor: pointer; outline: none;">
                <option value="all">All Categories</option>
                
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
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
</div>