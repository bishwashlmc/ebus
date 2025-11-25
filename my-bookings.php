<?php
// my_bookings.php
session_start();
include 'config.php';

// debug - show errors while developing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show message
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION['user_id'];

// Simple test: check if user has bookings (optional debug output)
// You can remove this block later
$testStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
$testStmt->execute([$userId]);
$bookingCount = (int)$testStmt->fetchColumn();

if ($bookingCount === 0) {
    $bookings = [];
} else {
    // Fetch bookings with route and bus details using your actual columns
    $stmt = $pdo->prepare("
        SELECT
            b.id AS booking_id,
            b.seat_numbers,
            b.total_price,
            b.booked_on,
            r.route_name,
            r.from_city,
            r.to_city,
            r.travel_date,
            buses.bus_number
        FROM bookings b
        LEFT JOIN routes r ON b.route_id = r.id
        LEFT JOIN buses ON b.bus_id = buses.id
        WHERE b.user_id = ?
        ORDER BY b.booked_on DESC
    ");
    $stmt->execute([$userId]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>My Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background:#2b6cb0; color:#fff; }
        .no-bookings { text-align: center; color: #666; margin-top: 40px; }
        .small { font-size: 0.9em; color: #555; }
    </style>
</head>
<body>

<h2>My Booking History</h2>

<?php if (empty($bookings)): ?>
    <p class="no-bookings">You have no bookings yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Bus Number</th>
                <th>Route</th>
                <th>Travel Date</th>
                <th>Seats</th>
                <th>Total Price</th>
                <th>Booked On</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['booking_id']) ?></td>
                    <td><?= htmlspecialchars($b['bus_number'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars(($b['route_name'] ?: ($b['from_city'].' → '.$b['to_city']))) ?></td>
                    <td><?= htmlspecialchars($b['travel_date'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($b['seat_numbers']) ?></td>
                    <td>Rs. <?= htmlspecialchars(number_format($b['total_price'], 2)) ?></td>
                    <td class="small"><?= htmlspecialchars($b['booked_on']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
