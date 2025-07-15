<?php
include 'header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking Success - ebus Nepal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            text-align: center;
            padding: 60px 20px;
        }
        .success-message {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            font-size: 24px;
            max-width: 450px;
        }
        .success-message a {
            color: #fff;
            text-decoration: underline;
            margin-top: 20px;
            display: inline-block;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="success-message">
        <h1>Booking Successful!</h1>
        <p>Thank you for booking your seat with ebus Nepal.</p>
        <p>Your seat(s) have been reserved successfully.</p>
        <a href="index.php">Back to Home</a> <!-- or your homepage -->
    </div>
</body>
</html>
