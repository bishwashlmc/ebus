
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

// Get already booked seats
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
    <meta charset="UTF-8">
    <title>Seat Booking - eBus Nepal</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .bus-container { max-width: 460px; margin: 40px auto; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .seat-side { display: flex; gap: 8px; }
        .seat {
            background: #f2f2f2;
            padding: 12px 14px;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            min-width: 45px;
            user-select: none;
            border: 1px solid #ccc;
            font-weight: 500;
            transition: 0.2s;
        }
        .seat.selected { background:#27ae60; color:white; }
        .seat.booked { background:#c0392b; color:white; cursor:not-allowed; }
        .aisle-space { width: 15px; }
        #totalPrice { font-weight:bold; text-align:center; margin-top:15px; }
        #payBtn {
            display:block; margin:25px auto; padding:14px 25px;
            background:#5a2ea6; color:white; border:none; 
            border-radius:6px; cursor:pointer; font-size:17px;
        }
        #payBtn:disabled { background:#999; cursor:not-allowed; }

    </style>
</head>
<body>

    <section class="page-hero">
        <div class="container">
            <h1>Book Your Seat</h1>
            <p>
                Route: <?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?> | 
                Bus: <?= htmlspecialchars($route['bus_number']) ?> |
                Date: <?= htmlspecialchars($route['travel_date']) ?>
            </p>
        </div>
    </section>

    <div class="container">

        
        <form id="bookingForm" method="POST" action="initiate_esewa.php">

            <input type="hidden" id="route_id" name="route_id" value="<?= htmlspecialchars($routeId) ?>">
            <input type="hidden" id="bus_id" name="bus_id" value="<?= htmlspecialchars($busId) ?>">
            <input type="hidden" id="price_per_seat" name="price_per_seat" value="<?= htmlspecialchars($pricePerSeat) ?>">
            <input type="hidden" id="selectedSeatsJSON" name="selectedSeatsJSON">

            <div class="bus-container">

                <!-- Seat Header -->
                <div class="row" style="margin-bottom:10px; font-weight:bold;">
                    <div class="seat-side">
                        <div style="width:45px; text-align:center;">A</div>
                        <div style="width:45px; text-align:center;">A</div>
                    </div>
                    <div class="aisle-space"></div>
                    <div class="seat-side">
                        <div style="width:45px; text-align:center;">B</div>
                        <div style="width:45px; text-align:center;">B</div>
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

                echo "<div class='row' style='justify-content:center; gap:6px;'>";
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

            <p id="selectedSeat" style="text-align:center; margin-top:20px; font-weight:bold;"></p>
            <p id="totalPrice">Total Price: Rs. 0</p>

            <button id="payBtn" type="submit">Proceed to Pay with esewa</button>

        </form>
    </div>

  <!-- Make login state available -->
<script>
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
</script>

<!-- 2. Then load seat-selection logic -->
<script src="booking.js"></script>



</body>
</html>
