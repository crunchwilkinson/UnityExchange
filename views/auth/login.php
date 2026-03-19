<div class="auth-section">

<div class="catalog-header">
        <div>
            <h1>Login to UnityExchange</h1>
            <p>Sign in to access your account.</p>
        </div>
        <a href="<?php echo $_ENV['APP_URL']; ?>/product/" class="btn-secondary">
            <i class="fa fa-arrow-left btn-icon-left"></i>Back to Marketplace
        </a>
    </div>

    <div class="auth-container">
        <form action="<?php echo $_ENV['APP_URL']; ?>/auth/login" method="POST">
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

        <p class="auth-link-container">Don't have an account? <a class="auth-link" href="<?php echo $_ENV['APP_URL']; ?>/auth/register">Register here</a></p>
    </div>
</div>