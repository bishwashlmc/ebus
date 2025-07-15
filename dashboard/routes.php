<?php
include 'header.php';
include 'config.php';

$success = $error = '';

if (isset($_POST['add_route'])) {
    $from = $_POST['from_city'];
    $to = $_POST['to_city'];
    $distance = $_POST['distance'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $date = $_POST['travel_date'];

    $stmt = $pdo->prepare("INSERT INTO routes (from_city, to_city, distance, duration, price, travel_date) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$from, $to, $distance, $duration, $price, $date])) {
        $success = "Route added successfully!";
    } else {
        $error = "Failed to add route.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['route_id'], $_POST['bus_id']) && !isset($_POST['add_route'])) {
    $routeId = $_POST['route_id'];
    $busId = $_POST['bus_id'];

    $stmt = $pdo->prepare("UPDATE routes SET bus_id = ? WHERE id = ?");
    if ($stmt->execute([$busId, $routeId])) {
        // Seat Generation Logic
        $seatCheck = $pdo->prepare("SELECT COUNT(*) FROM seats WHERE route_id = ? AND bus_id = ?");
        $seatCheck->execute([$routeId, $busId]);
        $seatCount = $seatCheck->fetchColumn();

        if ($seatCount == 0) {
            $stmtSeats = $pdo->prepare("SELECT total_seats FROM buses WHERE id = ?");
            $stmtSeats->execute([$busId]);
            $totalSeats = $stmtSeats->fetchColumn();

            if ($totalSeats) {
                $pdo->beginTransaction();
                try {
                    $a = 1;
                    $b = 1;
                    for ($i = 1; $i <= $totalSeats; $i++) {
                        $seatLabel = ($i % 2 == 0) ? 'b' . $b++ : 'a' . $a++;
                        $insertSeat = $pdo->prepare("INSERT INTO seats (bus_id, route_id, seat_number) VALUES (?, ?, ?)");
                        $insertSeat->execute([$busId, $routeId, $seatLabel]);
                    }
                    $pdo->commit();
                    $success = "Bus assigned and seats generated successfully.";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to generate seats: " . $e->getMessage();
                }
            }
        } else {
            $success = "Bus assigned successfully.";
        }
    } else {
        $error = "Failed to assign bus.";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM routes WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: routes.php?deleted=true");
        exit;
    } else {
        $error = "Failed to delete route.";
    }
}

$routes = $pdo->query("SELECT r.*, b.bus_number, b.total_seats FROM routes r LEFT JOIN buses b ON r.bus_id = b.id ORDER BY r.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$buses = $pdo->query("SELECT * FROM buses ORDER BY bus_number ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
<div class="container">

    <?php if (isset($_GET['deleted'])): ?>
        <div class="message success">Route deleted successfully!</div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <input type="text" name="from_city" placeholder="From City" required>
        <input type="text" name="to_city" placeholder="To City" required>
        <input type="number" name="distance" placeholder="Distance (km)" min="0" step="any" required>
        <input type="text" name="duration" placeholder="Duration" required>
        <input type="number" name="price" placeholder="Price (Rs)" min="0" required>
        <input type="date" name="travel_date" required>
        <button type="submit" name="add_route" class="btn">Add Route</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Route</th>
            <th>Distance</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Travel Date</th>
            <th>Assigned Bus</th>
            <th>Assign Bus</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($routes as $route): ?>
            <tr>
                <td><?= $route['id'] ?></td>
                <td><?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?></td>
                <td><?= $route['distance'] ?> km</td>
                <td><?= htmlspecialchars($route['duration']) ?></td>
                <td>Rs. <?= $route['price'] ?></td>
                <td><?= $route['travel_date'] ?></td>
                <td><?= htmlspecialchars($route['bus_number'] ?? 'Not Assigned') ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <select name="bus_id" required>
                            <option value="">Select Bus</option>
                            <?php foreach ($buses as $bus): ?>
                                <option value="<?= $bus['id'] ?>" <?= $bus['id'] == $route['bus_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bus['bus_number']) ?> (<?= $bus['total_seats'] ?> seats)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn">Assign</button>
                    </form>
                </td>
                <td>
                    <a href="update_route.php?id=<?= $route['id'] ?>">Edit</a> |
                    <a href="routes.php?delete=<?= $route['id'] ?>" onclick="return confirm('Are you sure you want to delete this route?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
