<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Manage Products</h1>
            <p>View and manage all products listed on the marketplace.</p>
        </div>
        <a href="/UnityExchange/admin" class="btn-primary">← Back to Admin Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" alt="Product Image" class="product-image"></td>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>

                            <td>
                                <?php
                                if (!empty($product['price'])) {
                                    echo '$' . number_format($product['price'], 2);
                                } else {
                                    echo '<em class="text-muted">N/A</em>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                if (!empty($product['stock_quantity'])) {
                                    echo htmlspecialchars($product['stock_quantity']);
                                } else {
                                    echo '<em class="text-muted">0</em>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                $date = new DateTime($product['created_at']);
                                echo $date->format('M j, Y');
                                ?>
                            </td>

                            <td class="action-buttons product-action-buttons">
                                <form action="/UnityExchange/admin/deleteProduct/<?php echo $product['id']; ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to completely delete product <?php echo htmlspecialchars($product['name']); ?>? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <button type="submit" class="btn-delete">
                                        Delete
                                    </button>
                                </form>
                               
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-table-message">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>