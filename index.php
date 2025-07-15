<?php
include 'header.php';
include 'config.php';

$popularStmt = $pdo->prepare("SELECT * FROM routes WHERE popular = 1 LIMIT 3");
$popularStmt->execute();
$popularRoutes = $popularStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ebus Nepal - Online Bus Ticket Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="home.css">
</head>

<body>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Book Bus Tickets Online</h1>
                    <p>Travel across Nepal with comfort and convenience</p>

                    <div class="search-box">
                        <form class="search-form" id="busSearchForm" method="GET" action="routes.php">
                            <div class="form-group">
                                <label for="from">From</label>
                                <select id="from" name="from" required>
                                    <option value="" disabled selected>Select Departure</option>
                                    <option value="Kathmandu">Kathmandu</option>
                                    <option value="Pokhara">Pokhara</option>
                                    <option value="Butwal">Butwal</option>
                                    <option value="Chitwan">Chitwan</option>
                                    <option value="Biratnagar">Biratnagar</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="to">To</label>
                                <select id="to" name="to" required>
                                    <option value="" disabled selected>Select Destination</option>
                                    <option value="Kathmandu">Kathmandu</option>
                                    <option value="Pokhara">Pokhara</option>
                                    <option value="Butwal">Butwal</option>
                                    <option value="Chitwan">Chitwan</option>
                                    <option value="Biratnagar">Biratnagar</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" id="date" name="date" required>
                            </div>
                            <button type="submit" class="search-btn">Search Buses</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="section-title">
                    <h2>Why Choose ebus Nepal?</h2>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-bus"></i></div>
                        <h3>Wide Network</h3>
                        <p>Connect to over 50 destinations across Nepal with our extensive bus network.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-ticket-alt"></i></div>
                        <h3>Easy Booking</h3>
                        <p>Book your tickets in just a few clicks from the comfort of your home.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Safe Travel</h3>
                        <p>Travel with trusted operators following all safety protocols.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-headset"></i></div>
                        <h3>24/7 Support</h3>
                        <p>Our customer support team is available round the clock to assist you.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="popular-routes">
            <div class="container">
                <div class="section-title">
                    <h2>Popular Routes</h2>
                </div>
                <div class="routes-grid">
                    <?php foreach ($popularRoutes as $route): ?>
                        <div class="route-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <?php if (!empty($route['image'])): ?>
                                <img src="<?= htmlspecialchars($route['image']) ?>" alt="route image" style="width: 100%; height: 180px; object-fit: cover;">
                            <?php endif; ?>
                            <div style="padding: 20px;">
                                <h3><?= htmlspecialchars($route['from_city']) ?> → <?= htmlspecialchars($route['to_city']) ?></h3>
                                <p>Distance: <?= htmlspecialchars($route['distance']) ?> km</p>
                                <p>Duration: <?= htmlspecialchars($route['duration']) ?></p>
                                <p>Price: Rs. <?= htmlspecialchars($route['price']) ?></p>
                                <a href="book.php?route_id=<?= $route['id'] ?>" class="btn book-now-btn">Book Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
    </main>

    <?php include 'footer.php'; ?>
    
    <script src="main.js"></script>
    <script src="searchRedirect.js"></script>
</body>
</html>
