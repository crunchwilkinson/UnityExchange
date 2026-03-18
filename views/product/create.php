<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>List a new Product</h1>
            <p>Create a new listing for your item.</p>
        </div>
        <a href="/UnityExchange/product/" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Back to Marketplace
        </a>
    </div>

    <div class="admin-form-container">
        <form action="/UnityExchange/product/store" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="name">What are you selling?</label>
                <input type="text" id="name" name="name" placeholder="e.g., Samsung Galaxy S21" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="" disabled selected>Select a category...</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Other / Miscellaneous</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description & Condition</label>
                <textarea id="description" name="description" rows="5" placeholder="Describe the item, its condition, and any accessories included..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group form-col">
                    <label for="price">Price (R)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                </div>

                <div class="form-group form-col">
                    <label for="stock_quantity">Quantity Available</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="1" value="1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="product_image">Upload a Photo (JPG, PNG, WEBP)</label>
                <input type="file" id="product_image" name="product_image" accept="image/jpeg, image/png, image/webp" class="form-file-input">
                <p class="help-text">Clear, well-lit photos sell faster.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Post Item</button>
            </div>
        </form>
    </div>
</div>


  