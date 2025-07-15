<?php
include 'header.php';
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $routeId = $_POST['route_id'];
    $popular = isset($_POST['popular']) ? 1 : 0;
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        $browserPath = 'uploads/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $browserPath;
        } else {
            echo "<script>alert('Image upload failed. Please check folder permissions.');</script>";
        }
    }

    if ($imagePath) {
        $stmt = $pdo->prepare("UPDATE routes SET popular = :popular, image = :image WHERE id = :id");
        $stmt->execute([
            'popular' => $popular,
            'image' => $imagePath,
            'id' => $routeId
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE routes SET popular = :popular WHERE id = :id");
        $stmt->execute([
            'popular' => $popular,
            'id' => $routeId
        ]);
    }
}

$routes = $pdo->query("SELECT * FROM routes")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Popular Routes</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container">
    <h1>Manage Popular Routes</h1>
    <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
        <thead>
        <tr style="background-color: #34495e; color: white;">
            <th>Route</th>
            <th>Distance</th>
            <th>Price</th>
            <th>Popular</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($routes as $route): ?>
            <tr style="background-color: #ecf0f1;">
                <td><?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?></td>
                <td><?= htmlspecialchars($route['distance']) ?> km</td>
                <td>Rs. <?= htmlspecialchars($route['price']) ?></td>
                <td><?= $route['popular'] ? 'Yes' : 'No' ?></td>
                <td>
                    <?php if (!empty($route['image'])): ?>
                        <img src="<?= htmlspecialchars($route['image']) ?>" alt="route image" style="height: 50px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                        <label>
                            <input type="checkbox" name="popular" <?= $route['popular'] ? 'checked' : '' ?>> Popular
                        </label><br><br>
                        <input type="file" name="image" accept="image/*" style="font-size: 12px;"><br><br>
                        <button type="submit" name="update_popular" class="btn">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
