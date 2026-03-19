<div class="products-section">
	<div class="details-breadcrumb">
		<a href="javascript:history.back()">
			<i class="fa fa-arrow-left btn-icon-left"></i>Go Back
		</a>
	</div>

	<div class="product-details-layout">
		<div class="product-details-image-card">
			<img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/products/<?php echo htmlspecialchars($product['image_file'] ?? 'default_product.png'); ?>"
				alt="<?php echo htmlspecialchars($product['name']); ?>"
				class="product-details-image">
		</div>

		<div class="product-details-info-card">
			<h1 class="product-details-title"><?php echo htmlspecialchars($product['name']); ?></h1>

			<p class="product-details-price">R <?php echo number_format((float)$product['price'], 2); ?></p>

			<div class="product-details-meta">
				<div class="meta-row">
					<span class="meta-label">Availability</span>
					<?php if ((int)$product['stock_quantity'] > 0): ?>
						<span class="stock-badge in-stock">In Stock (<?php echo (int)$product['stock_quantity']; ?>)</span>
					<?php else: ?>
						<span class="stock-badge sold-out">Sold Out</span>
					<?php endif; ?>
				</div>

				<div class="meta-row">
					<span class="meta-label">Seller</span>
					<span class="meta-value">
                        <a href="<?php echo $_ENV['APP_URL']; ?>/profile/details/<?php echo $product['user_id']; ?>" class="seller-link">
							<?php echo htmlspecialchars($product['seller_name']); ?>
						</a>
					</span>
				</div>

				<?php if (!empty($product['seller_email'])): ?>
					<div class="meta-row">
						<span class="meta-label">Contact</span>
						<a href="mailto:<?php echo htmlspecialchars($product['seller_email']); ?>" class="meta-value link-value">
							<?php echo htmlspecialchars($product['seller_email']); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if (!empty($product['created_at'])): ?>
					<div class="meta-row">
						<span class="meta-label">Listed</span>
						<span class="meta-value"><?php echo date('d M Y', strtotime($product['created_at'])); ?></span>
					</div>
				<?php endif; ?>
			</div>

			<div class="product-details-actions">
				<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['user_id'] == $product['user_id']): ?>
					<a href="<?php echo $_ENV['APP_URL']; ?>/product/edit/<?php echo $product['id']; ?>" class="btn-primary">Edit Product</a>
				<?php elseif (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
					<a class="btn-primary" id="add-to-cart-button" data-id="<?php echo $product['id']; ?>">Add to cart</a>
				<?php else: ?>
					<a href="<?php echo $_ENV['APP_URL']; ?>/auth/login" class="btn-primary">Login to Add to Cart</a>
				<?php endif; ?>

			</div>
		</div>
	</div>

	<div class="product-description-card">
		<h2>Description</h2>
		<p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
	</div>
</div>