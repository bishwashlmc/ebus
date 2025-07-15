<?php
include 'header.php';
include 'config.php';

$buses = $pdo->query("SELECT * FROM buses")->fetchAll(PDO::FETCH_ASSOC);

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
        $stmt = $pdo->prepare("SELECT * FROM seats WHERE bus_id = ? AND route_id = ?");
        $stmt->execute([$selectedBusId, $routeId]);
        $seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Handle reset: mark all seats as not booked and clear user info
if (isset($_POST['reset']) && $selectedBusId && $route) {
    $stmt = $pdo->prepare("UPDATE seats SET is_booked = 0, user_id = NULL, booked_on = NULL WHERE bus_id = ? AND route_id = ?");
    $stmt->execute([$selectedBusId, $route['id']]);

    // Optionally, you can also clear bookings table entries if you want:
    // $stmt2 = $pdo->prepare("DELETE FROM bookings WHERE bus_id = ? AND route_id = ?");
    // $stmt2->execute([$selectedBusId, $route['id']]);

    header("Location: seat_manage.php?bus_id=" . $selectedBusId);
    exit;
}
?>

<div class="container">
    <h2>Seat Management Panel</h2>

    <form method="GET">
        <label>Select Bus:</label>
        <select name="bus_id" onchange="this.form.submit()">
            <option value="">-- Choose Bus --</option>
            <?php foreach ($buses as $bus): ?>
                <option value="<?= $bus['id'] ?>" <?= $selectedBusId == $bus['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($bus['bus_number']) ?> (<?= $bus['total_seats'] ?> seats)
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($route): ?>
        <p><strong>Route:</strong> <?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?></p>

        <form method="POST" style="margin-top: 20px;">
            <button type="submit" name="reset" onclick="return confirm('Reset all booked seats?')">Reset Bookings</button>
        </form>

        <div class="bus-container" style="margin-top: 30px;">
            <h3>Seat Layout</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php if (empty($seats)): ?>
                    <p>No seats defined for this bus and route.</p>
                <?php else: ?>
                    <?php foreach ($seats as $seat): 
                        $label = htmlspecialchars($seat['seat_number']);
                        $class = $seat['is_booked'] ? 'booked' : 'available';
                    ?>
                        <div class="seat <?= $class ?>"> <?= $label ?> </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <style>
            .seat {
                width: 50px;
                text-align: center;
                padding: 10px;
                border-radius: 6px;
                font-weight: bold;
                color: white;
                user-select: none;
            }
            .available {
                background-color: #2ecc71;
            }
            .booked {
                background-color: #e74c3c;
            }
        </style>

    <?php elseif ($selectedBusId): ?>
        <p>No route assigned for this bus yet.</p>
    <?php endif; ?>
</div>
