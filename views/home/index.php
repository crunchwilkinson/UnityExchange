<div class="home-section">

    <div class="hero-banner">
        <div class="hero-content">
            <h1>Empowering Local Trade</h1>
            <p>Connect directly with buyers and sellers in your community. UnityExchange makes informal trading secure, fast, and simple.</p>
            <div class="hero-actions">
                <a href="<?php echo $_ENV['APP_URL']; ?>/product" class="btn-primary">Shop the Marketplace</a>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/product/create" class="btn-secondary">Start Selling</a>
                <?php else: ?>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/register" class="btn-secondary">Join Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2 class="section-title">How It Works</h2>
        <div class="info-grid">

            <div class="info-card">
                <div class="info-icon"><i class="fa fa-handshake icon-navy"></i></div>
                <h3>Connect Locally</h3>
                <p>Discover unique goods, services, and opportunities directly from people in your area.</p>
            </div>

            <div class="info-card">
                <div class="info-icon"><i class="fa fa-shield-alt icon-gold"></i></div>
                <h3>Secure Agreements</h3>
                <p>Lock in prices and reserve items safely using our transparent digital cart and checkout system.</p>
            </div>

            <div class="info-card">
                <div class="info-icon"><i class="fa fa-chart-line icon-emerald"></i></div>
                <h3>Grow Your Hustle</h3>
                <p>Set up your digital storefront in minutes and reach more customers without expensive overhead fees.</p>
            </div>

        </div>
    </div>

    <div class="products-grid">
        <div class="home-section-header">
            <h2 class="section-title home-section-title">Featured Listings</h2>
            <a href="<?php echo $_ENV['APP_URL']; ?>/product" class="home-section-link">View All →</a>
        </div>

        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php 
                            // Fallback to default avatar if none exists
                            $image = !empty($product['image_file']) ? $product['image_file'] : 'default_product.png'; 
                        ?>

                        <a href="<?php echo $_ENV['APP_URL']; ?>/product/details/<?php echo $product['id']; ?>" class="product-image-link">
                            <img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/products/<?php echo htmlspecialchars($image); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="product-image">
                        </a>

                        <div class="product-card-body">

                            <h3 class="product-title">
                                <a href="<?php echo $_ENV['APP_URL']; ?>/product/details/<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>

                            <p class="product-price">
                                R <?php echo number_format($product['price'], 2); ?>
                            </p>

                            <div class="product-footer">
                                <span class="seller-info">
                                    <div class="seller-info">
                                        Sold by:
                                        <strong>
                                            <a href="<?php echo $_ENV['APP_URL']; ?>/profile/details/<?php echo $product['user_id']; ?>" class="seller-link">
                                                <?php echo htmlspecialchars($product['seller_name']); ?>
                                            </a>
                                        </strong>
                                    </div>
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
                <p>No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="bottom-cta">
        <h2>Ready to turn your skills into income?</h2>
        <p>Join hundreds of local entrepreneurs already trading on the platform.</p>
        <a href="<?php echo $_ENV['APP_URL']; ?>/product/create" class="btn-secondary">List An Item Today</a>
    </div>

</div>