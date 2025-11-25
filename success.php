<?php
include 'header.php';
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$bookingId = $_GET['booking_id'] ?? null;

if (!$bookingId) {
    echo "<div class='container'><p>Invalid booking reference.</p></div>";
    exit;
}

// Fetch booking details with JOIN to get all info
$stmt = $pdo->prepare("
    SELECT 
        b.*,
        r.from_city,
        r.to_city,
        r.travel_date,
        r.duration,
        bus.bus_number,
        u.name as user_name,
        u.email,
        u.phone
    FROM bookings b
    JOIN routes r ON b.route_id = r.id
    JOIN buses bus ON b.bus_id = bus.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "<div class='container'><p>Booking not found or access denied.</p></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - eBus Nepal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .success-icon {
            text-align: center;
            font-size: 80px;
            color: #27ae60;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .success-title {
            text-align: center;
            color: #27ae60;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .success-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .ticket-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px;
            border-radius: 10px;
            color: white;
            margin-bottom: 25px;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed rgba(255,255,255,0.3);
        }
        .booking-id {
            font-size: 14px;
            opacity: 0.9;
        }
        .route-info {
            text-align: center;
            margin: 20px 0;
        }
        .route-cities {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .route-arrow {
            display: inline-block;
            margin: 0 15px;
            font-size: 28px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .detail-item {
            background: rgba(255,255,255,0.1);
            padding: 12px;
            border-radius: 6px;
        }
        .detail-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: bold;
        }
        .seats-display {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }
        .seats-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        .seats-numbers {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .total-amount {
            background: #27ae60;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        .total-label {
            font-size: 14px;
            margin-bottom: 5px;
            opacity: 0.9;
        }
        .total-value {
            font-size: 36px;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-primary {
            background: #5a2ea6;
            color: white;
        }
        .btn-primary:hover {
            background: #4a1e96;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(90,46,166,0.3);
        }
        .btn-secondary {
            background: white;
            color: #333;
            border: 2px solid #e0e0e0;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #ccc;
        }
        .btn-print {
            background: #3498db;
            color: white;
        }
        .btn-print:hover {
            background: #2980b9;
        }
        .note-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 25px;
            border-radius: 4px;
        }
        .note-box p {
            margin: 5px 0;
            color: #856404;
            font-size: 14px;
        }
        @media print {
            .action-buttons, .note-box { display: none; }
            .success-container { box-shadow: none; }
        }
        @media (max-width: 600px) {
            .detail-grid { grid-template-columns: 1fr; }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="success-container">
            <div class="success-icon">✓</div>
            <h1 class="success-title">Booking Confirmed!</h1>
            <p class="success-subtitle">Your bus ticket has been successfully booked</p>
            
            <!-- Ticket Design -->
            <div class="ticket-box">
                <div class="ticket-header">
                    <div>
                        <div style="font-size: 18px; font-weight: bold;">eBus Nepal</div>
                        <div style="font-size: 12px; opacity: 0.8;">Digital Bus Ticket</div>
                    </div>
                    <div class="booking-id">
                        #<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>
                
                <div class="route-info">
                    <div class="route-cities">
                        <?= htmlspecialchars($booking['from_city']) ?>
                        <span class="route-arrow">→</span>
                        <?= htmlspecialchars($booking['to_city']) ?>
                    </div>
                </div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Travel Date</div>
                        <div class="detail-value"><?= date('d M Y', strtotime($booking['travel_date'])) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Duration</div>
                        <div class="detail-value"><?= htmlspecialchars($booking['duration'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Bus Number</div>
                        <div class="detail-value"><?= htmlspecialchars($booking['bus_number']) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Passenger</div>
                        <div class="detail-value"><?= htmlspecialchars($booking['user_name']) ?></div>
                    </div>
                </div>
                
                <div class="seats-display">
                    <div class="seats-label">Your Seat Numbers</div>
                    <div class="seats-numbers"><?= htmlspecialchars($booking['seat_numbers']) ?></div>
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="total-amount">
                <div class="total-label">Total Amount Paid</div>
                <div class="total-value">Rs. <?= number_format($booking['total_price'], 2) ?></div>
            </div>
            
            <!-- Passenger Details -->
            <div class="info-box">
                <h3 style="margin-top: 0; margin-bottom: 15px; color: #333;">Passenger Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value"><?= htmlspecialchars($booking['user_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($booking['email']) ?></span>
                </div>
                <?php if (!empty($booking['phone'])): ?>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?= htmlspecialchars($booking['phone']) ?></span>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <span class="info-label">Booking Date:</span>
                    <span class="info-value"><?= date('d M Y, h:i A', strtotime($booking['booked_on'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Status:</span>
                    <span class="info-value" style="color: #27ae60;">✓ <?= ucfirst(htmlspecialchars($booking['payment_status'])) ?></span>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="note-box">
                <p><strong>📧 Confirmation Email:</strong> A booking confirmation has been sent to your email.</p>
                <p><strong>🎫 Show This Ticket:</strong> Please show this ticket (printed or on mobile) while boarding.</p>
                <p><strong>⏰ Arrive Early:</strong> Please reach the boarding point at least 15 minutes before departure.</p>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-print">🖨️ Print Ticket</button>
                <a href="my_bookings.php" class="btn btn-primary">📋 My Bookings</a>
                <a href="index.php" class="btn btn-secondary">🏠 Home</a>
            </div>
        </div>
    </div>

</body>
</html>