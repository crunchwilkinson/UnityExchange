<div class="cart-section">

    <div class="catalog-header">
        <div>
            <h1>Order #<?php echo htmlspecialchars($order['id']); ?></h1>
            <p>Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/order" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Back to Order History
        </a>
    </div>

    <div class="cart-layout">

        <div class="cart-items">

            <?php foreach ($items as $item): ?>
                <div class="cart-item cart-item-readonly">

                    <?php 
                        // Fallback to default avatar if none exists
                        $image = !empty($item['image_file']) ? $item['image_file'] : 'default_product.png'; 
                    ?>

                    <img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/products/<?php echo htmlspecialchars($image); ?>"
                        alt="<?php echo htmlspecialchars($item['product']['name']); ?>">

                    <div class="item-details">
                        <h3>
                            <a href="<?php echo $_ENV['APP_URL']; ?>/product/details/<?php echo $item['product_id']; ?>">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </a>
                        </h3>
                        <p>Sold by:
                            <strong>
                                <a href="<?php echo $_ENV['APP_URL']; ?>/profile/details/<?php echo $item['seller_id']; ?>" class="seller-link">
                                    <?php echo htmlspecialchars($item['seller_name']); ?>
                                </a>
                            </strong>
                        </p>

                        <p class="text-highlight-blue">
                            R <?php echo number_format($item['price_at_purchase'], 2); ?> each
                        </p>
                    </div>

                    <div class="item-quantity">
                        <label>Purchased</label>
                        <p class="qty-display-value text-highlight-blue">
                            <?php echo htmlspecialchars($item['quantity']); ?>
                        </p>
                    </div>

                    <div class="item-actions">
                        <label class="action-label-small">Subtotal</label>
                        <p class="no-margin text-highlight-green">
                            R <?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary">
            <h2>Receipt Summary</h2>

            <div>
                <span>Status</span>
                <?php
                // Cleaned up badge logic utilizing the .pending class
                $status = strtolower($order['status']);
                if ($status === 'completed') {
                    $badgeClass = 'stock-badge in-stock';
                    $statusText = 'Completed';
                } elseif ($status === 'cancelled') {
                    $badgeClass = 'stock-badge sold-out';
                    $statusText = 'Cancelled';
                } else {
                    $badgeClass = 'stock-badge pending';
                    $statusText = 'Pending';
                }
                ?>
                <span class="<?php echo $badgeClass; ?> badge-md">
                    <?php echo htmlspecialchars($statusText); ?>
                </span>
            </div>

            <div>
                <span>Items Subtotal</span>
                <span>R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>

            <div>
                <span>Total</span>
                <span>R <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>

            <?php if ($status === 'pending'): ?>
                <form action="<?php echo $_ENV['APP_URL']; ?>/order/complete/<?php echo $order['id']; ?>" method="POST" class="no-margin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <button type="submit" class="btn-primary btn-green">
                        Mark as Received
                    </button>
                </form>
                <p class="receipt-help-text">
                    Have you received these items from the seller?
                </p>

            <?php endif; ?>

        </div>

    </div>
</div>