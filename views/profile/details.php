<div class="products-section">
    <div class="catalog-header">
        <div>
            <h1>Seller Profile</h1>
            <p>Learn more about this UnityExchange member.</p>
        </div>
        <a href="javascript:history.back()" class="btn-secondary">← Go Back</a>
    </div>

    <div class="admin-form-container seller-profile-card">
        
        <div class="seller-profile-header">
            <?php $avatar = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_avatar.png'; ?>
            <img src="/UnityExchange/assets/images/users/<?php echo htmlspecialchars($avatar); ?>" 
                 alt="Profile Picture" 
                 class="seller-avatar">
            
            <div>
                <h2 class="seller-username">
                    <?php echo htmlspecialchars($user['username']); ?>
                </h2>
                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="btn-primary btn-sm">
                    <i class="fa fa-envelope"></i> Contact Seller
                </a>
            </div>
        </div>

        <div>
            <h3 class="bio-section-title">About Me</h3>
            <div class="bio-content-box">
                <?php if (!empty($user['description'])): ?>
                    <?php echo nl2br(htmlspecialchars($user['description'])); ?>
                <?php else: ?>
                    <em class="empty-bio-text">This user has not provided a biography yet.</em>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>