<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnityExchange</title>
    <?php 
    // Automatically get the exact timestamp of when the CSS file was last modified (Cache Busting)
    $css_version = filemtime(__DIR__ . '/../assets/css/admin_site.css'); 
    ?>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $_ENV['APP_URL']; ?>/favicon.png">
    <link rel="stylesheet" href="<?php echo $_ENV['APP_URL']; ?>/assets/css/admin_site.css?v=<?php echo $css_version; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <a href="<?php echo $_ENV['APP_URL']; ?>/admin">UnityExchange</a>
        </div>
        
        <input type="checkbox" id="admin-menu-toggle" class="menu-toggle">
        <label for="admin-menu-toggle" class="menu-icon">
            <i class="fa fa-bars"></i>
        </label>

        <nav>
            <div class="nav-links">
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin"><i class="fa fa-home"></i> Home</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/users"><i class="fa fa-users"></i> Users</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/products"><i class="fa fa-shopping-cart"></i> Products</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/admin/transactions"><i class="fa fa-exchange-alt"></i> Transactions</a>

                <div class="nav-search">
                    <div class="search-container">
                        <input type="text" id="adminLiveSearch" placeholder="Search records...">
                        <button type="button" aria-label="Search"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="auth-links">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']) . " (" . implode(', ', $_SESSION['roles']) . ")"; ?></p>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/login">Login</a>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>