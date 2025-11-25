<?php
include 'header.php';
include 'config.php';

$buses = $pdo->query("SELECT * FROM buses ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$selectedBusId = $_GET['bus_id'] ?? null;
$route = null;
$seats = [];

if ($selectedBusId) {
    // Get the assigned route for the selected bus
    $stmt = $pdo->prepare("SELECT * FROM routes WHERE bus_id = ? LIMIT 1");
    $stmt->execute([$selectedBusId]);
    $route = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($route) {
        $routeId = $route['id'];

        // Fetch all seats for the selected bus and route with booking status
        $stmt = $pdo->prepare("SELECT * FROM seats WHERE bus_id = ? AND route_id = ? ORDER BY id");
        $stmt->execute([$selectedBusId, $routeId]);
        $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Handle reset: mark all seats as not booked and clear user info
if (isset($_POST['reset']) && $selectedBusId && $route) {
    $stmt = $pdo->prepare("UPDATE seats SET is_booked = 0, user_id = NULL, booked_on = NULL WHERE bus_id = ? AND route_id = ?");
    $stmt->execute([$selectedBusId, $route['id']]);
    
    // Also clear from bookings table
    $stmt2 = $pdo->prepare("DELETE FROM bookings WHERE bus_id = ? AND route_id = ?");
    $stmt2->execute([$selectedBusId, $route['id']]);

    header("Location: seat_manage.php?bus_id=" . $selectedBusId);
    exit;
}

// Get selected bus details
$selectedBus = null;
if ($selectedBusId) {
    $busStmt = $pdo->prepare("SELECT * FROM buses WHERE id = ?");
    $busStmt->execute([$selectedBusId]);
    $selectedBus = $busStmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Management Panel - eBus Nepal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 40px auto; padding: 20px; }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .select-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .select-box label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }
        
        .select-box select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            background: white;
        }
        
        .select-box select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .route-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .route-info strong {
            color: #667eea;
        }
        
        .reset-btn {
            background: #e74c3c;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: 0.3s;
        }
        
        .reset-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .bus-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .bus-container h3 {
            margin-top: 0;
            color: #333;
            font-size: 22px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .driver-section {
            background: #34495e;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .seat-layout {
            max-width: 500px;
            margin: 0 auto;
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
        
        .aisle-space {
            width: 30px;
        }
        
        .seat {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
            color: white;
            user-select: none;
            font-size: 14px;
            transition: 0.3s;
            border: 2px solid transparent;
        }
        
        .seat.available {
            background-color: #27ae60;
            cursor: pointer;
        }
        
        .seat.available:hover {
            background-color: #229954;
            transform: scale(1.05);
            border-color: white;
        }
        
        .seat.booked {
            background-color: #e74c3c;
            cursor: not-allowed;
            opacity: 0.8;
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .legend-box {
            width: 40px;
            height: 40px;
            border-radius: 6px;
        }
        
        .stats-box {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .no-route-message {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #666;
        }
        
        .no-route-message h3 {
            color: #e74c3c;
            font-size: 24px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1 style="margin: 0;">🎫 Seat Management Panel</h1>
        <p style="margin: 10px 0 0; opacity: 0.9;">Manage bus seats and view booking status</p>
    </div>

    <div class="select-box">
        <form method="GET">
            <label for="bus_select">🚌 Select Bus:</label>
            <select name="bus_id" id="bus_select" onchange="this.form.submit()">
                <option value="">-- Choose a Bus --</option>
                <?php foreach ($buses as $bus): ?>
                    <option value="<?= $bus['id'] ?>" <?= $selectedBusId == $bus['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($bus['bus_number']) ?> (<?= $bus['total_seats'] ?> seats)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedBus && $route): ?>
        
        <div class="route-info">
            <strong>📍 Route:</strong> <?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?> | 
            <strong>📅 Date:</strong> <?= htmlspecialchars($route['travel_date']) ?>
        </div>
        
        <?php
        // Calculate statistics
        $totalSeats = $selectedBus['total_seats'];
        $bookedCount = 0;
        foreach ($seats as $seat) {
            if ($seat['is_booked']) $bookedCount++;
        }
        $availableCount = $totalSeats - $bookedCount;
        ?>
        
        <div class="stats-box">
            <div class="stat-card">
                <div class="stat-value"><?= $totalSeats ?></div>
                <div class="stat-label">Total Seats</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #e74c3c;"><?= $bookedCount ?></div>
                <div class="stat-label">Booked</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #27ae60;"><?= $availableCount ?></div>
                <div class="stat-label">Available</div>
            </div>
        </div>

        <form method="POST" style="margin-bottom: 20px; text-align: center;">
            <button type="submit" name="reset" class="reset-btn" 
                    onclick="return confirm('⚠️ This will reset all booked seats for this bus and route. Continue?')">
                🔄 Reset All Bookings
            </button>
        </form>

        <div class="bus-container">
            <div class="driver-section">
                🚗 DRIVER
            </div>
            
            <h3>Seat Layout</h3>
            
            <div class="seat-layout">
                <?php if (empty($seats)): ?>
                    <p style="text-align: center; color: #e74c3c;">⚠️ No seats defined for this bus and route.</p>
                <?php else: ?>
                    <?php
                    // Group seats by row for better display
                    $seatIndex = 0;
                    $totalSeatsCount = count($seats);
                    $regularRows = floor(($totalSeatsCount - 6) / 4);
                    
                    // Display regular rows (4 seats each)
                    for ($row = 0; $row < $regularRows; $row++) {
                        echo "<div class='row'>";
                        
                        // Left side (2 A seats)
                        echo "<div class='seat-side'>";
                        for ($col = 0; $col < 2; $col++) {
                            if ($seatIndex < $totalSeatsCount) {
                                $seat = $seats[$seatIndex];
                                $class = $seat['is_booked'] ? 'booked' : 'available';
                                echo "<div class='seat $class'>" . htmlspecialchars($seat['seat_number']) . "</div>";
                                $seatIndex++;
                            }
                        }
                        echo "</div>";
                        
                        echo "<div class='aisle-space'></div>";
                        
                        // Right side (2 B seats)
                        echo "<div class='seat-side'>";
                        for ($col = 0; $col < 2; $col++) {
                            if ($seatIndex < $totalSeatsCount) {
                                $seat = $seats[$seatIndex];
                                $class = $seat['is_booked'] ? 'booked' : 'available';
                                echo "<div class='seat $class'>" . htmlspecialchars($seat['seat_number']) . "</div>";
                                $seatIndex++;
                            }
                        }
                        echo "</div>";
                        
                        echo "</div>";
                    }
                    
                    // Display last row (remaining seats)
                    if ($seatIndex < $totalSeatsCount) {
                        echo "<div class='row' style='justify-content: center; gap: 8px; margin-top: 20px; padding-top: 20px; border-top: 2px dashed #ddd;'>";
                        while ($seatIndex < $totalSeatsCount) {
                            $seat = $seats[$seatIndex];
                            $class = $seat['is_booked'] ? 'booked' : 'available';
                            echo "<div class='seat $class'>" . htmlspecialchars($seat['seat_number']) . "</div>";
                            $seatIndex++;
                        }
                        echo "</div>";
                    }
                    ?>
                <?php endif; ?>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box" style="background-color: #27ae60;"></div>
                    <span><strong>Available</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-box" style="background-color: #e74c3c;"></div>
                    <span><strong>Booked</strong></span>
                </div>
            </div>
        </div>

    <?php elseif ($selectedBusId && !$route): ?>
        <div class="no-route-message">
            <h3>⚠️ No Route Assigned</h3>
            <p>This bus is not assigned to any route yet.</p>
            <p>Please assign this bus to a route first.</p>
        </div>
    <?php elseif (!$selectedBusId): ?>
        <div class="no-route-message">
            <h3>👆 Select a Bus</h3>
            <p>Choose a bus from the dropdown above to view seat layout and bookings.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>