<?php
include 'header.php';
include 'config.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "SELECT * FROM routes WHERE (from_city = :from OR :from = '') AND (to_city = :to OR :to = '')";
$stmt = $pdo->prepare($sql);
$stmt->execute(['from' => $from, 'to' => $to]);
$routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bus Routes | ebus Nepal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <section class="page-hero">
        <div class="container">
            <h1>Our Bus Routes</h1>
            <p>Explore our extensive network of bus routes across Nepal</p>
        </div>
    </section>

    <section class="routes-section" style="padding: 60px 0;">
        <div class="container">
            <div class="section-title">
                <h2>Popular Routes</h2>
                <p>Choose from our most popular routes with comfortable buses and affordable prices</p>
            </div>

            <div class="route-search-box" style="background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <form id="routeSearchForm" method="GET" action="routes.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div class="form-group" style="display: flex; flex-direction: column;">
                        <label for="routeFrom" style="margin-bottom: 8px; font-weight: 500; color: #333;">From</label>
                        <select name="from" id="routeFrom" style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                            <option value="">Select City</option>
                            <option value="Kathmandu" <?= ($from === 'Kathmandu') ? 'selected' : '' ?>>Kathmandu</option>
                            <option value="Pokhara" <?= ($from === 'Pokhara') ? 'selected' : '' ?>>Pokhara</option>
                            <option value="Chitwan" <?= ($from === 'Chitwan') ? 'selected' : '' ?>>Chitwan</option>
                            <option value="Lumbini" <?= ($from === 'Lumbini') ? 'selected' : '' ?>>Lumbini</option>
                            <option value="Dharan" <?= ($from === 'Dharan') ? 'selected' : '' ?>>Dharan</option>
                        </select>
                    </div>
                    <div class="form-group" style="display: flex; flex-direction: column;">
                        <label for="routeTo" style="margin-bottom: 8px; font-weight: 500; color: #333;">To</label>
                        <select name="to" id="routeTo" style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                            <option value="">Select City</option>
                            <option value="Kathmandu" <?= ($to === 'Kathmandu') ? 'selected' : '' ?>>Kathmandu</option>
                            <option value="Pokhara" <?= ($to === 'Pokhara') ? 'selected' : '' ?>>Pokhara</option>
                            <option value="Chitwan" <?= ($to === 'Chitwan') ? 'selected' : '' ?>>Chitwan</option>
                            <option value="Lumbini" <?= ($to === 'Lumbini') ? 'selected' : '' ?>>Lumbini</option>
                            <option value="Dharan" <?= ($to === 'Dharan') ? 'selected' : '' ?>>Dharan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn" style="align-self: flex-end;">Search Routes</button>
                </form>
            </div>

            <div class="routes-grid" id="routesContainer" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php if ($routes): ?>
                    <?php foreach ($routes as $row): ?>
                        <div class='route-card'>
                            <h3><?= htmlspecialchars($row['from_city']) ?> → <?= htmlspecialchars($row['to_city']) ?></h3>
                            <p>Distance: <?= htmlspecialchars($row['distance']) ?> km | Duration: <?= htmlspecialchars($row['duration']) ?></p>
                            <p>Price: Rs. <?= htmlspecialchars($row['price']) ?></p>
                            <p>Date: <?= htmlspecialchars($row['travel_date']) ?></p>
                            <a href="#" class="btn book-now-btn" data-route-id="<?= $row['id'] ?>">Book Now</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No routes found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="all-routes" style="padding: 60px 0; background-color: #f5f5f5;">
        <div class="container">
            <div class="section-title">
                <h2>All Available Routes</h2>
                <p>Browse through all our available routes across Nepal</p>
            </div>

           <div class="route-table" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
    <table style="width: 100%; border-collapse: separate; border-spacing: 0 15px;">
        <thead>
            <tr style="background-color: #2c3e50; color: white;">
                <th style="padding: 15px; text-align: left;">Route</th>
                <th style="padding: 15px; text-align: left;">Distance</th>
                <th style="padding: 15px; text-align: left;">Duration</th>
                <th style="padding: 15px; text-align: left;">Price</th>
                <th style="padding: 15px; text-align: left;">Date</th>
                <th style="padding: 15px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
                        <?php
                        $allRoutes = $pdo->query("SELECT * FROM routes")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($allRoutes as $row):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['from_city']) ?> → <?= htmlspecialchars($row['to_city']) ?></td>
                                <td><?= htmlspecialchars($row['distance']) ?> km</td>
                                <td><?= htmlspecialchars($row['duration']) ?></td>
                                <td>Rs. <?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['travel_date']) ?></td>
                                <td><a href="#" class="btn book-now-btn" data-route-id="<?= $row['id'] ?>">Book Now</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="main.js"></script>
    <script src="routes.js"></script>
</body>
</html>
