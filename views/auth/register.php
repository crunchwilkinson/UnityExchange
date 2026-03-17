<div class="auth-section">

    <div class="catalog-header">
        <div>
            <h1>Create an Account</h1>
            <p>Create a new account to access UnityExchange.</p>
        </div>
        <a href="/UnityExchange/product/" class="btn-secondary">← Back to Marketplace</a>
    </div>

    <div class="auth-container">
        <form action="/UnityExchange/auth/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($old_username ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old_email ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn-primary">Register</button>
        </form>

        <p class="auth-link-container">Already have an account? <a class="auth-link" href="/UnityExchange/auth/login">Login here</a></p>
    </div>
</div>