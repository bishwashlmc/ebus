<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - eBus Nepal' : 'eBus Nepal - Online Bus Booking' ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container">
        <div class="header-content">
            <div class="logo">ebus <span>Nepal</span></div>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?= ($current_page == '' || $current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
                    <li><a href="routes.php" class="<?= ($current_page == 'routes.php') ? 'active' : '' ?>">Routes</a></li>
                    <li><a href="services.php" class="<?= ($current_page == 'services.php') ? 'active' : '' ?>">Services</a></li>
                    <li><a href="about.php" class="<?= ($current_page == 'about.php') ? 'active' : '' ?>">About Us</a></li>
                    <li><a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="my-bookings.php">My Bookings</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <a href="profile.php" class="welcome-msg">👋 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login">Login</a>
                    <a href="register.php" class="register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>