<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to book.");
}

$userId = $_SESSION['user_id'];
$routeId = $_POST['route_id'];
$busId = $_POST['bus_id'];
$selectedSeats = $_POST['selectedSeats'];

if (!is_array($selectedSeats) || empty($selectedSeats)) {
    die("Invalid seat selection.");
}

// Sanitize seat input
$sanitizedSeats = array_map(function ($seat) {
    return htmlspecialchars(trim($seat));
}, $selectedSeats);
$seatString = implode(',', $sanitizedSeats);

// Recalculate price from database
$stmt = $pdo->prepare("SELECT price FROM routes WHERE id = ?");
$stmt->execute([$routeId]);
$pricePerSeat = $stmt->fetchColumn();
$totalPrice = count($sanitizedSeats) * $pricePerSeat;

// Check already booked seats from seats table for stronger consistency
$placeholders = rtrim(str_repeat('?,', count($sanitizedSeats)), ',');
$sql = "SELECT seat_number FROM seats WHERE bus_id = ? AND route_id = ? AND seat_number IN ($placeholders) AND is_booked = 1";
$params = array_merge([$busId, $routeId], $sanitizedSeats);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($bookedSeats)) {
    $conflicts = implode(', ', $bookedSeats);
    die("Sorry, these seats are already booked: $conflicts");
}

// Insert booking
$stmt = $pdo->prepare("INSERT INTO bookings (user_id, route_id, bus_id, seat_numbers, total_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

if ($stmt->execute([$userId, $routeId, $busId, $seatString, $totalPrice])) {
    // Update seats table to mark seats as booked
    $updateSeatStmt = $pdo->prepare("UPDATE seats SET is_booked = 1, user_id = ?, booked_on = NOW() WHERE bus_id = ? AND route_id = ? AND seat_number = ?");
    foreach ($sanitizedSeats as $seatNum) {
        $updateSeatStmt->execute([$userId, $busId, $routeId, $seatNum]);
    }
    
    header("Location: booking_success.php");
    exit;
} else {
    echo "Booking failed. Error: " . $stmt->errorInfo()[2];
}
