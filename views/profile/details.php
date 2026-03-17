<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>Seller Profile</h1>
            <p>Learn more about this UnityExchange member.</p>
        </div>
        <a href="javascript:history.back()" class="btn-secondary">← Go Back</a>
    </div>

    <div class="admin-form-container">
        
        <div style="display: flex; align-items: center; gap: 25px; border-bottom: 1px solid #edf2f7; padding-bottom: 25px;">
            <?php $avatar = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_avatar.png'; ?>
            <img src="/UnityExchange/assets/images/users/<?php echo htmlspecialchars($avatar); ?>" 
                 alt="Profile Picture" 
                 style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #3498db; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            
            <div>
                <h2 style="margin: 0 0 5px 0; color: #2d3748; font-size: 2rem;">
                    <?php echo htmlspecialchars($user['username']); ?>
                </h2>
                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="btn-primary" style="display: inline-block; padding: 8px 16px; font-size: 0.9rem; margin-top: 10px;">
                    <i class="fa fa-envelope" style="margin-right: 5px;"></i> Contact Seller
                </a>
            </div>
        </div>

        <div>
            <h3 style="color: #4a5568; margin-top: 0; margin-bottom: 15px; font-size: 1.25rem;">About Me</h3>
            <div style="color: #4a5568; line-height: 1.8; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <?php if (!empty($user['description'])): ?>
                    <?php echo nl2br(htmlspecialchars($user['description'])); ?>
                <?php else: ?>
                    <em style="color: #a0aec0;">This user has not provided a biography yet.</em>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>