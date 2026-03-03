<div class="admin-section">

    <div class="catalog-header">
        <div>
            <h1>Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>
            <p>View and manage user accounts, permissions, and roles.</p>
        </div>
        <a href="/UnityExchange/admin/users" class="btn-back">← Back to Users</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-form-container">
        <form action="/UnityExchange/admin/update/<?php echo htmlspecialchars($user['id']); ?>" method="POST">
            <input type = "hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <fieldset class="form-group roles-group">
                <legend>Assign Roles</legend>
                <p class="help-text">Select all permissions that apply to this user.</p>

                <div class="checkbox-grid">
                    <?php foreach ($all_roles as $role): ?>
                        <div class="checkbox-wrapper">
                            <input type="checkbox"
                                id="role_<?php echo $role['id']; ?>"
                                name="roles[]"
                                value="<?php echo $role['id']; ?>"
                                <?php echo in_array($role['name'], $user_current_roles) ? 'checked' : ''; ?>>

                            <label for="role_<?php echo $role['id']; ?>">
                                <?php echo htmlspecialchars(ucfirst($role['name'])); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="admin-button">Update User</button>
            </div>

        </form>
    </div>

</div>