<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>Edit Product</h1>
            <p>Update your listing details</p>
        </div>
        <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>" class="btn-secondary">← Back to Product</a>
    </div>

    <div class="edit-product-layout">
        <div class="admin-form-container">
            <div class="current-image-preview">
                <p class="current-image-label">Current Image:</p>
                <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="current-image-img">
            </div>

            <form action="/UnityExchange/product/update/<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="" disabled>Select a category...</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1" selected>Other / Miscellaneous</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description & Condition</label>
                    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label for="price">Price (R)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>

                    <div class="form-group form-col">
                        <label for="stock_quantity">Quantity Available</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product_image">Update Image (Optional)</label>
                    <input type="file" id="product_image" name="product_image" accept="image/jpeg, image/png, image/webp" class="form-file-input">
                    <p class="help-text">Leave empty to keep current image. Accepted formats: JPEG, PNG, WEBP (Max 5MB)</p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="admin-form-container delete-container">
            <h3 class="delete-warning-title">Danger Zone</h3>
            <p class="delete-warning-text">Deleting this product is permanent and cannot be undone.</p>
            <form action="/UnityExchange/product/delete/<?php echo $product['id']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn-primary btn-danger" style="width: 100%;">Delete Product</button>
            </form>
        </div>
    </div>
</div>
