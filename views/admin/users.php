<div class="admin-section">
    <div class="catalog-header">
        <div>
            <h1>Manage Users</h1>
            <p>View and manage user accounts, permissions, and roles.</p>
        </div>
        <a href="/UnityExchange/admin" class="btn-secondary">← Back to Admin Dashboard</a>
    </div>

    <div style="background-color: #2c3e50; padding: 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); margin-bottom: 50px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding-bottom: 20px;">
            <div style="color: #fff; font-size: 1.2rem; font-weight: 600;">
                <i class="fa fa-users" style="color: #3498db; margin-right: 8px;"></i> Filter Users
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <select id="userRoleFilter" style="padding: 10px 15px; border: 1px solid #34495e; border-radius: 6px; background-color: #2c3e50; color: #fff; font-size: 0.95rem; min-width: 250px; cursor: pointer; outline: none;">
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
                        <td colspan="6" class="empty-table-message" style="text-align: center; padding: 40px; color: #718096;">
                            <i class="fa fa-user-slash" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #cbd5e0;"></i>
                            No users match this role filter.
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>