<div class="products-section">
    
    <div class="catalog-header">
        <div>
            <h1>My Profile</h1>
            <p>Manage your account settings and security preferences.</p>
        </div>
    </div>

    <div class="edit-product-layout">
        
       <div class="admin-form-container">
            <h2 style="font-size: 1.4rem; color: #2d3748; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #edf2f7; padding-bottom: 10px;">
                <i class="fa fa-user" style="color: #3498db; margin-right: 8px;"></i> Account Details
            </h2>
            
            <form action="/UnityExchange/profile/updateDetails" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px dashed #e2e8f0;">
                    <?php 
                        // Fallback to default avatar if none exists
                        $avatar = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_avatar.png'; 
                    ?>
                    <img src="/UnityExchange/assets/images/users/<?php echo htmlspecialchars($avatar); ?>" 
                         alt="Profile Picture" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #edf2f7; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 15px;">
                    
                    <div style="width: 100%;">
                        <label for="profile_picture" style="display: block; font-weight: 600; color: #4a5568; margin-bottom: 8px; text-align: center;">Change Avatar</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png, image/webp" 
                               style="display: block; margin: 0 auto; font-size: 0.9rem; color: #718096;">
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

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="admin-form-container">
            <h2 style="font-size: 1.4rem; color: #2d3748; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #edf2f7; padding-bottom: 10px;">
                <i class="fa fa-lock" style="color: #e53e3e; margin-right: 8px;"></i> Security
            </h2>
            
            <form action="/UnityExchange/profile/updatePassword" method="POST">
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

                <div class="form-actions">
                    <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);">Update Password</button>
                </div>
            </form>
        </div>

    </div>
</div>