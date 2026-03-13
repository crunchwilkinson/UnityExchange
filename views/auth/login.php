<div class="auth-section">

<div class="catalog-header">
        <div>
            <h1>Login to UnityExchange</h1>
            <p>Sign in to access your account.</p>
        </div>
        <a href="/UnityExchange/product/" class="btn-secondary">← Back to Marketplace</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="auth-container">
        <form action="/UnityExchange/auth/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old_email ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="auth-link-container">Don't have an account? <a class="auth-link" href="/UnityExchange/auth/register">Register here</a></p>
    </div>
</div>