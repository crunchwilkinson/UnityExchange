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

    <div class="product-grid-wrapper">
        
        <div class="filter-bar-wrapper">
            <div class="filter-title">
                <i class="fa fa-filter filter-icon"></i> Browse by Category
            </div>

            <div class="filter-bar">
                <select id="categoryFilter" class="category-select">
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
        </div>

        <div class="product-grid">
            
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?php echo $product['category_id']; ?>">
                        <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>" class="product-image-link">
                            <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        </a>
                        <div class="product-card-body">
                            <h3 class="product-title">
                                <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            </h3>
                            <p class="product-price">R <?php echo number_format($product['price'], 2); ?></p>
                            <div class="product-footer">
                                <span class="seller-info">
                                    Sold by: 
                                    <strong>
                                        <a href="/UnityExchange/profile/details/<?php echo $product['user_id']; ?>" class="seller-link">
                                            <?php echo htmlspecialchars($product['seller_name']); ?>
                                        </a>
                                    </strong>
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
            <?php endif; ?>

            <div id="js-empty-state" class="empty-state empty-state-transparent" style="display: none;">
                <h3>No matches found</h3>
                <p>There are currently no items listed in this specific category.</p>
                <button type="button" id="clearFilterBtn" class="btn-primary filter-clear-btn">View All Items</button>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-state empty-state-transparent">
                    <h3>No items found</h3>
                    <p>Your Listings are currently empty. List an item today!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>