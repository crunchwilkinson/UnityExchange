<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
    <title>UnityExchange</title>
    <link rel="stylesheet" href="/UnityExchange/assets/css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <a href="/UnityExchange/home">UnityExchange</a>
        </div>

        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="menu-icon">
            <i class="fa fa-bars"></i>
        </label>

        <nav>
            <div class="nav-links">
                <a href="/UnityExchange/home"><i class="fa fa-home"></i> Home</a>
                <a href="/UnityExchange/product"><i class="fa fa-th-large"></i> Marketplace</a>
                <a href="/UnityExchange/product/myListings"><i class="fa fa-box"></i> My Listings</a>
                <a href="/UnityExchange/cart"><i class="fa fa-shopping-cart"></i> Cart</a>
                <div class="nav-dropdown">
                    <span class="dropdown-trigger"><i class="fa fa-exchange-alt"></i> Transactions <i class="fa fa-caret-down"></i></span>
                    <div class="dropdown-content">
                        <a href="/UnityExchange/order"><i class="fa fa-shopping-bag"></i> My Purchases</a>
                        <a href="/UnityExchange/sales"><i class="fa fa-store"></i> My Sales</a>
                    </div>
                </div>
                <div class="nav-search">
                    <form action="/UnityExchange/product" method="GET">
                        <input type="text" name="search" placeholder="Search items..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" aria-label="Search"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="auth-links">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="nav-user-profile">
                        <?php $nav_avatar = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_avatar.png'; ?>
                        <a href="/UnityExchange/profile">
                            <img src="/UnityExchange/assets/images/users/<?php echo htmlspecialchars($nav_avatar); ?>" 
                                 alt="Avatar" 
                                 class="nav-avatar">
                        </a>
                        <p class="welcome-message">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']);?>
                        </p>
                    </div>
                    <a href="/UnityExchange/auth/logout">Logout</a>
                <?php else: ?>
                    <a href="/UnityExchange/auth/login">Login</a>
                    <a href="/UnityExchange/auth/register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>