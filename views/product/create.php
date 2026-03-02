<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>List a new Product</h1>
            <p>Create a new listing for your item.</p>
        </div>
        <a href="/UnityExchange/product/" class="btn-back" style="color: #4a5568; text-decoration: none; font-weight: 500;">← Back to Marketplace</a>
    </div>

    <div class="admin-form-container">
        <form action="/UnityExchange/product/store" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="name">What are you selling?</label>
                <input type="text" id="name" name="name" placeholder="e.g., Samsung Galaxy S21" required>
            </div>

            <div class="form-group">
                <label for="description">Description & Condition</label>
                <textarea id="description" name="description" rows="5" placeholder="Describe the item, its condition, and any accessories included..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 6px; font-family: inherit;" required></textarea>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label for="price">Price (R)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="stock_quantity">Quantity Available</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="1" value="1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="product_image">Upload a Photo (JPG, PNG, WEBP)</label>
                <input type="file" id="product_image" name="product_image" accept="image/jpeg, image/png, image/webp" style="display: block; margin-top: 5px;">
                <p class="help-text">Clear, well-lit photos sell faster.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Post Item</button>
            </div>
        </form>
    </div>
</div>


  