<?php
include 'header.php';
include 'config.php';

$success = '';
$error = '';


$id = $_GET['id'] ?? null;

if (!$id) {
    echo "Invalid Route ID";
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM routes WHERE id = ?");
$stmt->execute([$id]);
$route = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$route) {
    echo "Route not found.";
    exit;
}


if (isset($_POST['update_route'])) {
    $from = $_POST['from_city'];
    $to = $_POST['to_city'];
    $distance = $_POST['distance'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $date = $_POST['travel_date'];

    $updateStmt = $pdo->prepare("UPDATE routes SET from_city = ?, to_city = ?, distance = ?, duration = ?, price = ?, travel_date = ? WHERE id = ?");
   if ($updateStmt->execute([$from, $to, $distance, $duration, $price, $date, $id])) {
    header("Location:routes.php?updated=1");
    exit;
}

}
?>

<div class="container">
    <h2>Edit Route</h2>

    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST" style="margin-top: 20px;">
        <input type="text" name="from_city" value="<?= htmlspecialchars($route['from_city']) ?>" placeholder="From City" required>
        <input type="text" name="to_city" value="<?= htmlspecialchars($route['to_city']) ?>" placeholder="To City" required>
        <input type="number" name="distance" value="<?= htmlspecialchars($route['distance']) ?>" placeholder="Distance (km)" required>
        <input type="text" name="duration" value="<?= htmlspecialchars($route['duration']) ?>" placeholder="Duration" required>
        <input type="number" name="price" value="<?= htmlspecialchars($route['price']) ?>" placeholder="Price (Rs)" required>
        <input type="date" name="travel_date" value="<?= htmlspecialchars($route['travel_date']) ?>" required>
        <button type="submit" name="update_route" class="btn">Update Route</button>
    </form>
</div>
