<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Manage Products</h1>
            <p>View and manage all products listed on the marketplace.</p>
        </div>
        <a href="/UnityExchange/admin" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i> Go Back
        </a>
    </div>

    <div class="admin-grid-wrapper">
        <div class="admin-filterbar-wrapper">
            <div class="admin-filter-title">
                <i class="fa fa-filter admin-filter-icon"></i> Filter Inventory
            </div>
            <div class="admin-filter-bar">
                <select id="categoryFilter" class="admin-filter-select">
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

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="product-row" data-category="<?php echo isset($product['category_id']) ? htmlspecialchars($product['category_id']) : ''; ?>">
                                <td><img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" alt="Product Image" class="product-image"></td>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>

                                <td class="text-nowrap">
                                    <?php
                                    if (!empty($product['price'])) {
                                        echo 'R ' . number_format($product['price'], 2);
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

                                <td class="text-nowrap">
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
                            <td colspan="8" class="empty-table-message">No products found in the database.</td>
                        </tr>
                    <?php endif; ?>

                    <tr id="js-empty-state" style="display: none;">
                        <td colspan="8" class="empty-table-message">
                            <i class="fa fa-folder-open empty-table-icon"></i>
                            No products match this category filter.
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>