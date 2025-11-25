<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get form data
$route_id = $_POST['route_id'] ?? null;
$bus_id = $_POST['bus_id'] ?? null;
$selected_seats = json_decode($_POST['selectedSeatsJSON'] ?? '[]', true);
$price_per_seat = $_POST['price_per_seat'] ?? 0;

if (!$route_id || !$bus_id || empty($selected_seats)) {
    die("Invalid booking data");
}

// Calculate total amount
$amount = count($selected_seats) * floatval($price_per_seat);
$tax_amount = 0;
$product_service_charge = 0;
$product_delivery_charge = 0;
$total_amount = $amount + $tax_amount + $product_service_charge + $product_delivery_charge;

// Get user details
try {
    $userStmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Get route details
try {
    $routeStmt = $pdo->prepare("SELECT from_city, to_city FROM routes WHERE id = ?");
    $routeStmt->execute([$route_id]);
    $route = $routeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$route) {
        die("Route not found");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Generate unique transaction UUID
$transaction_uuid = 'BUS-' . time() . '-' . rand(1000, 9999);

// Store booking details in session temporarily
$_SESSION['pending_booking'] = [
    'route_id' => $route_id,
    'bus_id' => $bus_id,
    'seats' => $selected_seats,
    'amount' => $total_amount,
    'transaction_uuid' => $transaction_uuid
];
echo "<pre>";
echo "Session stored successfully:\n";
print_r($_SESSION['pending_booking']);
echo "</pre>";

// eSewa Configuration (TEST/UAT)
$product_code = "EPAYTEST"; // Test merchant code
$secret_key = "8gBm/:&EnhH.1/q"; // Test secret key

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/ebus/";
$success_url = $base_url . "esewa_success.php";
$failure_url = $base_url . "esewa_failure.php";

// Generate signature using HMAC SHA256
$signed_field_names = "total_amount,transaction_uuid,product_code";
$message = "total_amount=" . $total_amount . ",transaction_uuid=" . $transaction_uuid . ",product_code=" . $product_code;
$signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }
        .loader-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #60bb46;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h3 { color: #333; margin: 0 0 10px; }
        p { color: #666; margin: 0; }
        .debug { 
            margin-top: 20px; 
            padding: 10px; 
            background: #f0f0f0; 
            border-radius: 5px;
            font-size: 12px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
        <h3>Redirecting to eSewa...</h3>
        <p>Please wait while we redirect you to the payment gateway.</p>
        
        <div class="debug">
            <strong>Payment Details:</strong><br>
            Amount: Rs. <?= number_format($total_amount, 2) ?><br>
            Transaction ID: <?= htmlspecialchars($transaction_uuid) ?><br>
            Seats: <?= htmlspecialchars(implode(', ', $selected_seats)) ?><br>
            Signature: <?= substr($signature, 0, 20) ?>...<br>
        </div>
    </div>

    <!-- eSewa Payment Form (v2 API) -->
    <form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" name="amount" value="<?= $amount ?>">
        <input type="hidden" name="tax_amount" value="<?= $tax_amount ?>">
        <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
        <input type="hidden" name="transaction_uuid" value="<?= $transaction_uuid ?>">
        <input type="hidden" name="product_code" value="<?= $product_code ?>">
        <input type="hidden" name="product_service_charge" value="<?= $product_service_charge ?>">
        <input type="hidden" name="product_delivery_charge" value="<?= $product_delivery_charge ?>">
        <input type="hidden" name="success_url" value="<?= $success_url ?>">
        <input type="hidden" name="failure_url" value="<?= $failure_url ?>">
        <input type="hidden" name="signed_field_names" value="<?= $signed_field_names ?>">
        <input type="hidden" name="signature" value="<?= $signature ?>">
    </form>

    <script>
        console.log("eSewa v2 Form Data:");
        console.log("Amount:", "<?= $amount ?>");
        console.log("Total Amount:", "<?= $total_amount ?>");
        console.log("Transaction UUID:", "<?= $transaction_uuid ?>");
        console.log("Product Code:", "<?= $product_code ?>");
        console.log("Signature:", "<?= $signature ?>");
        console.log("Success URL:", "<?= $success_url ?>");
        
        // Auto-submit form after 2 seconds
        setTimeout(function() {
            console.log("Submitting to eSewa v2 API...");
            document.getElementById('esewaForm').submit();
        }, 2000);
    </script>
</body>
</html>