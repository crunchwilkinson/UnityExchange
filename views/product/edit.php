<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>Edit Product</h1>
            <p>Update your listing details</p>
        </div>
        <a href="/UnityExchange/product/details/<?php echo $product['id']; ?>" class="btn-back" style="color: #4a5568; text-decoration: none; font-weight: 500;">← Back to Product</a>
    </div>

    <div class="edit-product-layout">
        <div class="admin-form-container">
            <div class="current-image-preview" style="margin-bottom: 25px; text-align: center;">
                <p style="margin-bottom: 10px; color: #718096; font-weight: 600;">Current Image:</p>
                <img src="/UnityExchange/assets/images/products/<?php echo htmlspecialchars($product['image_file']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     style="max-width: 300px; border-radius: 8px; border: 1px solid #e2e8f0;">
            </div>

            <form action="/UnityExchange/product/update/<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description & Condition</label>
                    <textarea id="description" name="description" rows="5" style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-family: inherit;" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="price">Price (R)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>

                    <div class="form-group" style="flex: 1;">
                        <label for="stock_quantity">Quantity Available</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product_image">Update Image (Optional)</label>
                    <input type="file" id="product_image" name="product_image" accept="image/jpeg, image/png, image/webp" style="display: block; margin-top: 5px;">
                    <p class="help-text">Leave empty to keep current image. Accepted formats: JPEG, PNG, WEBP (Max 5MB)</p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        <!-- Delete Product Section -->
        <div class="admin-form-container delete-container">
            <h3 style="color: #9b2c2c; margin-bottom: 15px; margin-top: 0;">Danger Zone</h3>
            <p style="color: #718096; margin-bottom: 20px; font-size: 0.9rem;">Deleting this product is permanent and cannot be undone.</p>
            <form action="/UnityExchange/product/delete/<?php echo $product['id']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" style="background: linear-gradient(135deg, #fc8181 0%, #c53030 100%); padding: 12px 20px; width: 100%; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Delete Product</button>
            </form>
        </div>
    </div>
</div>
