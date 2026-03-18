<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Manage Transactions</h1>
            <p>View all marketplace orders and force-update fulfillment statuses.</p>
        </div>
        <a href="/UnityExchange/admin" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="admin-grid-wrapper">
        <div class="admin-filterbar-wrapper">
            <div class="admin-filter-title">
                <i class="fa fa-exchange-alt admin-filter-icon"></i> Filter Orders
            </div>
            <div class="admin-filter-bar">
                <select id="statusFilter" class="admin-filter-select">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Buyer</th>
                        <th>Total Amount</th>
                        <th>Date Placed</th>
                        <th>Current Status</th>
                        <th>Admin Override</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="order-row" data-status="<?php echo htmlspecialchars($order['status']); ?>">
                                <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                <td>R <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                
                                <td>
                                    <?php 
                                        $status = strtolower($order['status']);
                                        $badgeClass = 'admin-badge-pending';
                                        if ($status === 'completed') $badgeClass = 'admin-badge-completed';
                                        if ($status === 'cancelled') $badgeClass = 'admin-badge-cancelled';
                                    ?>
                                    <span class="admin-badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($status)); ?>
                                    </span>
                                </td>

                                <td class="action-buttons">
                                    <form action="/UnityExchange/admin/updateOrderStatus/<?php echo $order['id']; ?>" method="POST" class="status-update-form">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        
                                        <select name="status" class="admin-filter-select status-select-sm">
                                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        
                                        <button type="submit" class="btn-edit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-table-message">No orders found in the database.</td>
                        </tr>
                    <?php endif; ?>

                    <tr id="js-order-empty-state" style="display: none;">
                        <td colspan="6" class="empty-table-message">
                            <i class="fa fa-box-open empty-table-icon"></i>
                            No orders match this status filter.
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>