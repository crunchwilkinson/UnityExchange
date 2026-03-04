    <footer>
        <p>&copy; 2026 UnityExchange Prototype</p>
    </footer>
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div id="js-flash-message" data-message="<?php echo htmlspecialchars($_SESSION['flash_success']) ?>"></div>
        <?php unset($_SESSION['flash_success']); ?> <!-- Clear the flash message after displaying it once -->
    <?php endif ?>
    <script src="/UnityExchange/assets/js/app.js"></script>
</body>
</html>