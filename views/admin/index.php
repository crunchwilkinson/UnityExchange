<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome to the UnityExchange command center.</p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/home" class="btn-secondary" target="_blank">
            <i class="fa fa-external-link-alt btn-icon-left"></i> View Live Site
        </a>
    </div>

    <div class="product-grid">

        <div class="product-card">
            <a href="<?php echo $_ENV['APP_URL']; ?>/admin/users" class="admin-icon-link gradient-purple">
                <i class="fa fa-users admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="<?php echo $_ENV['APP_URL']; ?>/admin/users">User Management</a>
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
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/users" class="btn-primary btn-block">
                    Manage Users
                </a>
            </div>
        </div>

        <div class="product-card">
            <a href="<?php echo $_ENV['APP_URL']; ?>/admin/products" class="admin-icon-link gradient-blue">
                <i class="fa fa-shopping-cart admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="<?php echo $_ENV['APP_URL']; ?>/admin/products">Inventory Control</a>
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
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/products" class="btn-primary btn-block">
                    Manage Inventory
                </a>
            </div>
        </div>

        <div class="product-card">
            <a href="<?php echo $_ENV['APP_URL']; ?>/admin/transactions" class="admin-icon-link gradient-green">
                <i class="fa fa-money-bill-wave admin-icon"></i>
            </a>
            <div class="product-card-body">
                <h3 class="product-title">
                    <a href="<?php echo $_ENV['APP_URL']; ?>/admin/transactions">Transactions</a>
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
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/transactions" class="btn-primary btn-block">
                    Manage Transactions
                </a>
            </div>
        </div>

    </div>
</div>