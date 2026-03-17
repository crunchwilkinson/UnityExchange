    <footer>
        <p>&copy; 2026 UnityExchange Prototype</p>
    </footer>
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="js-flash-message" data-message="<?php echo htmlspecialchars($_SESSION['flash_message']) ?>" data-type="<?php echo htmlspecialchars($_SESSION['flash_type']) ?>"></div>
        <?php unset($_SESSION['flash_message']); ?> <!-- Clear the flash message after displaying it once -->
    <?php endif ?>
    <script src="/UnityExchange/assets/js/app.js"></script>
</body>
</html>