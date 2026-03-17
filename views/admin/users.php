<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Manage Users</h1>
            <p>View and manage user accounts, permissions, and roles.</p>
        </div>
        <a href="/UnityExchange/admin" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <div class="admin-grid-wrapper">
        
        <div class="admin-filterbar-wrapper">
            <div class="admin-filter-title">
                <i class="fa fa-users admin-filter-icon"></i> Filter Users
            </div>
            
            <div class="admin-filter-bar">
                <select id="userRoleFilter" class="admin-filter-select">
                    <option value="all">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row" data-roles="<?php echo isset($user['roles']) ? htmlspecialchars(strtolower($user['roles'])) : ''; ?>">
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>

                                <td>
                                    <?php
                                    if (!empty($user['roles'])) {
                                        $rolesArray = explode(',', $user['roles']);
                                        foreach ($rolesArray as $role) {
                                            echo '<span class="role-badge">' . htmlspecialchars(trim(ucfirst($role))) . '</span>';
                                        }
                                    } else {
                                        echo '<em class="text-muted">None</em>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $date = new DateTime($user['created_at']);
                                    echo $date->format('M j, Y');
                                    ?>
                                </td>

                                <td class="action-buttons">
                                    <a href="/UnityExchange/admin/edit/<?php echo $user['id']; ?>" class="btn-edit">Edit</a>

                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form action="/UnityExchange/admin/delete/<?php echo $user['id']; ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to completely delete user <?php echo htmlspecialchars($user['username']); ?>? This cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <button type="submit" class="btn-delete">
                                                Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="self-label">(You)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-table-message">No users found in the database.</td>
                        </tr>
                    <?php endif; ?>

                    <tr id="js-user-empty-state" style="display: none;">
                        <td colspan="6" class="empty-table-message">
                            <i class="fa fa-user-slash empty-table-icon"></i>
                            No users match this role filter.
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>