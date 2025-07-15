<?php
include 'header.php';
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$routeId = $_GET['route_id'] ?? null;

if (!$routeId) {
    echo "<p>Invalid route selected.</p>";
    exit;
}

// Fetch route and bus info
$stmt = $pdo->prepare("
    SELECT r.*, b.bus_number, b.total_seats, b.id as bus_id 
    FROM routes r 
    LEFT JOIN buses b ON r.bus_id = b.id 
    WHERE r.id = ?
");
$stmt->execute([$routeId]);
$route = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$route || !$route['bus_number']) {
    echo "<p>No bus assigned for this route yet.</p>";
    exit;
}

$totalSeats = $route['total_seats'] ?? 40;
$pricePerSeat = $route['price'];
$busId = $route['bus_id'];

// 🔒 Fetch already booked seats
$bookedStmt = $pdo->prepare("SELECT seat_numbers FROM bookings WHERE route_id = ? AND bus_id = ?");
$bookedStmt->execute([$route['id'], $busId]);
$bookedSeatsRaw = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

$bookedSeats = [];
foreach ($bookedSeatsRaw as $seatList) {
    $bookedSeats = array_merge($bookedSeats, explode(',', $seatList));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Seat Booking - ebus Nepal</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .bus-container {
            max-width: 460px;
            margin: 40px auto;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .seat-side {
            display: flex;
            gap: 8px;
        }
        .seat {
            background: #f2f2f2;
            padding: 12px 14px;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.2s;
            min-width: 45px;
            user-select: none;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            font-weight: 500;
        }
        .seat:hover {
            background: #dfe6e9;
        }
        .seat.selected {
            background: #27ae60;
            color: white;
        }
        .seat.booked {
            background: #c0392b;
            color: white;
            cursor: not-allowed;
        }
        .aisle-space {
            width: 15px;
        }
        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        #totalPrice {
            font-weight: bold;
            text-align: center;
            margin-top: 15px;
        }
        button#submitBooking {
            display: block;
            margin: 20px auto;
            padding: 12px 25px;
            font-size: 16px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button#submitBooking:disabled {
            background-color: #999;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <section class="page-hero">
        <div class="container">
            <h1>Book Your Seat</h1>
            <p>Route: <?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?> | 
            Bus: <?= htmlspecialchars($route['bus_number']) ?> | 
            Date: <?= htmlspecialchars($route['travel_date']) ?></p>
        </div>
    </section>

    <div class="container">
        <form id="bookingForm" method="POST" action="process_booking.php">
            <input type="hidden" name="route_id" value="<?= htmlspecialchars($routeId) ?>">
            <input type="hidden" name="bus_id" value="<?= htmlspecialchars($busId) ?>">
            <input type="hidden" name="price_per_seat" value="<?= htmlspecialchars($pricePerSeat) ?>">

            <div class="bus-container" id="busLayout">
                <div class="row" style="margin-bottom: 10px; font-weight: bold;">
                    <div class="seat-side" style="gap: 8px;">
                        <div style="width: 45px; text-align: center;">A</div>
                        <div style="width: 45px; text-align: center;">A</div>
                    </div>
                    <div class="aisle-space"></div>
                    <div class="seat-side" style="gap: 8px;">
                        <div style="width: 45px; text-align: center;">B</div>
                        <div style="width: 45px; text-align: center;">B</div>
                    </div>
                </div>

                <?php
                $rows = floor(($totalSeats - 3) / 4); 
                $a = 1;
                $b = 1;

                for ($i = 0; $i < $rows; $i++) {
                    echo "<div class='row'>";

                    echo "<div class='seat-side'>";
                    for ($j = 0; $j < 2; $j++) {
                        $seatName = "a$a";
                        $bookedClass = in_array($seatName, $bookedSeats) ? "booked" : "";
                        echo "<div class='seat $bookedClass' data-seat='$seatName'>$seatName</div>";
                        $a++;
                    }
                    echo "</div>";

                    echo "<div class='aisle-space'></div>";

                    echo "<div class='seat-side'>";
                    for ($j = 0; $j < 2; $j++) {
                        $seatName = "b$b";
                        $bookedClass = in_array($seatName, $bookedSeats) ? "booked" : "";
                        echo "<div class='seat $bookedClass' data-seat='$seatName'>$seatName</div>";
                        $b++;
                    }
                    echo "</div>";

                    echo "</div>";
                }

                echo "<div class='row' style='justify-content: center; gap: 6px;'>";
                for ($j = 0; $j < 4; $j++) {
                    $seatName = "a$a";
                    $bookedClass = in_array($seatName, $bookedSeats) ? "booked" : "";
                    echo "<div class='seat $bookedClass' data-seat='$seatName'>$seatName</div>";
                    $a++;
                }

                for ($j = 0; $j < 2; $j++) {
                    $seatName = "b$b";
                    $bookedClass = in_array($seatName, $bookedSeats) ? "booked" : "";
                    echo "<div class='seat $bookedClass' data-seat='$seatName'>$seatName</div>";
                    $b++;
                }
                echo "</div>";
                ?>
            </div>

            <p id="selectedSeat" style="text-align: center; margin-top: 20px; font-weight: bold;"></p>
            <p id="totalPrice">Total Price: Rs. 0</p>

            <div class="legend">
                <span><div class="seat legend-seat">Available</div></span>
                <span><div class="seat selected legend-seat">Selected</div></span>
                <span><div class="seat booked legend-seat">Booked</div></span>
            </div>


            <button type="submit" id="submitBooking" disabled>Book Selected Seats</button>
        </form>
    </div>

    <script>
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    </script>
    <script src="booking.js"></script>
</body>
</html>
