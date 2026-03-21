<div class="products-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Profile</h1>
            <p>Manage your account settings and security preferences.</p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/home" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Go Back
        </a>
    </div>

    <div class="edit-product-layout">
        
       <div class="admin-form-container">
            <h2 class="form-section-header">
                <i class="fa fa-user icon-blue"></i> Account Details
            </h2>
            
            <form action="<?php echo $_ENV['APP_URL']; ?>/profile/updateDetails" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="avatar-upload-container">
                    <?php 
                        // Fallback to default avatar if none exists
                        $avatar = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_avatar.png'; 
                    ?>
                    <img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/users/<?php echo htmlspecialchars($avatar); ?>" 
                         alt="Profile Picture" 
                         class="avatar-preview">
                    
                    <div style="width: 100%;">
                        <label for="profile_picture" class="avatar-upload-label">Change Avatar</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png, image/webp" class="avatar-file-input">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">About Me / Bio</label>
                    <textarea id="description" name="description" rows="5" placeholder="Tell buyers a little about yourself, your business, or your products..."><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

       <div class="admin-form-container">
            <h2 class="form-section-header">
                <i class="fa fa-shield-alt icon-red"></i> Security Settings
            </h2>
            <p class="form-subtitle">
                Ensure your account is using a long, random password to stay secure.
            </p>
            
            <form action="<?php echo $_ENV['APP_URL']; ?>/profile/updatePassword" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required placeholder="Enter your current password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required placeholder="Minimum 8 characters" minlength="8">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Type new password again" minlength="8">
                </div>

                <div class="info-box">
                    <h4 class="info-box-header">
                        <i class="fa fa-info-circle"></i> Password Requirements:
                    </h4>
                    <ul class="info-box-list">
                        <li>Minimum of 8 characters long</li>
                        <li>Should not be a commonly used password</li>
                        <li>Cannot be exactly the same as your username</li>
                    </ul>
                </div>

                <div class="settings-row">
                    <div>
                        <h4 class="settings-row-title">Two-Factor Authentication</h4>
                        <p class="settings-row-desc">Add an extra layer of security.</p>
                    </div>
                    <span class="badge-neutral">Coming Soon</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-danger">Update Password</button>
                </div>
            </form>
        </div>

    </div>
</div>