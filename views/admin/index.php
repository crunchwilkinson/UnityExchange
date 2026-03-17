<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome to the UnityExchange command center.</p>
        </div>
        <a href="/UnityExchange/home" class="btn-secondary" target="_blank">
            <i class="fa fa-external-link-alt" style="margin-right: 8px;"></i> View Live Site
        </a>
    </div>

    <div class="product-grid">

        <div class="product-card">
            <a href="/UnityExchange/admin/users" class="admin-icon-link gradient-purple">
                <i class="fa fa-users admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="/UnityExchange/admin/users">User Management</a>
                </h3>
                <div class="metric-container metric-purple">
                    <div>
                        <span class="metric-value"><?php echo isset($total_users) ? number_format($total_users) : 0; ?></span>
                        <span class="metric-label">Registered Accounts</span>
                    </div>
                    <i class="fa fa-chart-line metric-icon-small"></i>
                </div>
                <p class="admin-card-description">
                    Review registered accounts, update email addresses, and manage administrative roles across the platform.
                </p>
                <a href="/UnityExchange/admin/users" class="btn-primary" style="text-align: center;">
                    Manage Users &rarr;
                </a>
            </div>
        </div>

        <div class="product-card">
            <a href="/UnityExchange/admin/products" class="admin-icon-link gradient-blue">
                <i class="fa fa-shopping-cart admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="/UnityExchange/admin/products">Inventory Control</a>
                </h3>
               <div class="metric-container metric-blue">
                    <div>
                        <span class="metric-value"><?php echo isset($total_products) ? number_format($total_products) : 0; ?></span>
                        <span class="metric-label">Active Listings</span>
                    </div>
                    <i class="fa fa-tags metric-icon-small"></i>
                </div>
                <p class="admin-card-description">
                    Monitor all active listings, moderate inappropriate content, and completely remove products from the database.
                </p>
                <a href="/UnityExchange/admin/products" class="btn-primary" style="text-align: center;">
                    Manage Inventory &rarr;
                </a>
            </div>
        </div>

        <div class="product-card">
            <a href="#" class="admin-icon-link gradient-green">
                <i class="fa fa-money-bill-wave admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="#">Transactions</a>
                </h3>
                <div class="metric-container metric-green">
                    <div>
                        <span class="metric-value"><?php echo isset($total_transactions) ? number_format($total_transactions) : 0; ?></span>
                        <span class="metric-label">Completed Sales</span>
                    </div>
                    <i class="fa fa-money-bill-wave metric-icon-small"></i>
                </div>
                <p class="admin-card-description">
                    Track marketplace sales, view order histories, and resolve transaction disputes between buyers and sellers.
                </p>
                <a href="#" class="btn-primary" style="text-align: center;">
                    Coming Soon
                </a>
            </div>
        </div>

    </div>
</div>