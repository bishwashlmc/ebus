<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$id = $_POST['id'];
$status = $_POST['status'];

// SAFETY: Only accept approved or rejected
if (!in_array($status, ['approved', 'rejected'])) {
    die("Invalid status");
}

// Update booking payment status
$stmt = $pdo->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
$updated = $stmt->execute([$status, $id]);

if (!$updated) {
    die("Database update failed");
}

// Fetch user info to send email
$q = $pdo->prepare("SELECT u.email, u.name AS fullname 
                    FROM bookings b 
                    JOIN users u ON b.user_id = u.id 
                    WHERE b.id = ?");
$q->execute([$id]);
$user = $q->fetch(PDO::FETCH_ASSOC);

// Email notification
$to = $user['email'];
$subject = "Your Booking Status Updated";
$message = "Hello " . $user['fullname'] . ",\n\nYour booking has been " . strtoupper($status) . ".\n\nThank you!";
$headers = "From: noreply@ebus.com";

// Optional mail
@mail($to, $subject, $message, $headers);

// Redirect back
header("Location: users.php?msg=Status updated successfully");
exit;
