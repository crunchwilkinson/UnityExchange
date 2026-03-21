<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
    <title>UnityExchange</title>
    <?php 
    // Automatically get the exact timestamp of when the CSS file was last modified (Cache Busting)
    $css_version = filemtime(__DIR__ . '/../assets/css/site.css'); ?>
    <?php $icon_version = filemtime(__DIR__ . '/../favicon.png'); ?>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $_ENV['APP_URL']; ?>/favicon.png?v=<?php echo $icon_version; ?>">
    <link rel="stylesheet" href="<?php echo $_ENV['APP_URL']; ?>/assets/css/site.css?v=<?php echo $css_version; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header class="navbar">
       <div class="logo">
            <a href="<?php echo $_ENV['APP_URL']; ?>/home">
                <img src="<?php echo $_ENV['APP_URL']; ?>/favicon.png?v=<?php echo $icon_version; ?>" alt="UnityExchange Logo" class="logo-img">
                UnityExchange
            </a>
        </div>

        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="menu-icon">
            <i class="fa fa-bars"></i>
        </label>

        <nav>
            <div class="nav-links">
                <a href="<?php echo $_ENV['APP_URL']; ?>/home"><i class="fa fa-home"></i> Home</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/product"><i class="fa fa-th-large"></i> Marketplace</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/product/myListings"><i class="fa fa-box"></i> My Listings</a>
                <a href="<?php echo $_ENV['APP_URL']; ?>/cart"><i class="fa fa-shopping-cart"></i> Cart</a>
                <div class="nav-dropdown">
                    <input type="checkbox" id="tx-dropdown-toggle" style="display: none;">
                    
                    <label for="tx-dropdown-toggle" class="dropdown-trigger">
                        <i class="fa fa-exchange-alt"></i> Transactions <i class="fa fa-caret-down"></i>
                    </label>
                    
                    <div class="dropdown-content">
                        <a href="<?php echo $_ENV['APP_URL']; ?>/order"><i class="fa fa-shopping-bag"></i> My Orders</a>
                        <a href="<?php echo $_ENV['APP_URL']; ?>/sales"><i class="fa fa-store"></i> My Sales</a>
                    </div>
                </div>
                <div class="nav-search">
                    <form action="<?php echo $_ENV['APP_URL']; ?>/product" method="GET">
                        <input type="text" name="search" placeholder="Search items..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="auth-links">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="nav-user-profile">
                        <?php $nav_avatar = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_avatar.png'; ?>
                        <a href="<?php echo $_ENV['APP_URL']; ?>/profile">
                            <img src="<?php echo $_ENV['APP_URL']; ?>/assets/images/users/<?php echo htmlspecialchars($nav_avatar); ?>"
                                alt="Avatar"
                                class="nav-avatar">
                        </a>
                        <p class="welcome-message">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </p>
                    </div>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/logout">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/login">Login</a>
                    <a href="<?php echo $_ENV['APP_URL']; ?>/auth/register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>