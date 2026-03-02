<div class="auth-section">
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="auth-container">
        <h2>Login to UnityExchange</h2>

        <form action="/UnityExchange/auth/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <p class="auth-link-container">Don't have an account? <a class="auth-link" href="/UnityExchange/auth/register">Register here</a></p>
    </div>
</div>