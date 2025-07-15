<?php
include 'header.php';
include 'config.php';
// session_start();

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // redirect to login if not logged in
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT id, name, email, phone, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch user booking history with route and bus details
$stmt = $pdo->prepare("
    SELECT b.id as booking_id, b.seat_numbers, b.total_price, b.booked_on,
           r.route_name, r.from_city, r.to_city, r.travel_date,
           bus.bus_number
    FROM bookings b
    JOIN routes r ON b.route_id = r.id
    JOIN buses bus ON b.bus_id = bus.id
    WHERE b.user_id = ?
    ORDER BY b.booked_on DESC
");
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Profile - ebus Nepal</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .profile-container {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f9f9f9;
        }
        .profile-info h2 {
            margin-bottom: 10px;
        }
        .profile-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #27ae60;
            color: white;
        }
        .no-bookings {
            margin-top: 20px;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-info">
            <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <p><strong>Member since:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
        </div>

        <h3>Your Booking History</h3>
        <?php if (count($bookings) === 0): ?>
            <p class="no-bookings">You have no bookings yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Route</th>
                        <th>Bus Number</th>
                        <th>Travel Date</th>
                        <th>Seats</th>
                        <th>Total Price (Rs.)</th>
                        <th>Booked On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['booking_id'] ?></td>
                            <td><?= htmlspecialchars($booking['from_city'] . " → " . $booking['to_city']) ?></td>
                            <td><?= htmlspecialchars($booking['bus_number']) ?></td>
                            <td><?= htmlspecialchars(date('F j, Y', strtotime($booking['travel_date']))) ?></td>
                            <td><?= htmlspecialchars($booking['seat_numbers']) ?></td>
                            <td><?= number_format($booking['total_price'], 2) ?></td>
                            <td><?= htmlspecialchars(date('F j, Y, g:i A', strtotime($booking['booked_on']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
