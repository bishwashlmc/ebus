<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | eBus Nepal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <nav>
        <ul>
            <li><a href="dashboard.php" class="<?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>">View</a></li>
            <li><a href="users.php" class="<?= ($currentPage === 'users.php') ? 'active' : '' ?>">User Management</a></li>
            <li><a href="routes.php" class="<?= ($currentPage === 'admin_routes.php') ? 'active' : '' ?>">Route Management</a></li>
            <li><a href="contacts.php" class="<?= ($currentPage === 'contacts.php') ? 'active' : '' ?>">Contact Management</a></li>
            <li><a href="seat_manage.php" class="<?= ($currentPage === 'buses.php') ? 'active' : '' ?>">Bus Management</a></li>
        </ul>
    </nav>
    <div class="right-side">
        <span class="welcome"><?= htmlspecialchars($admin_name); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="admin-container">
