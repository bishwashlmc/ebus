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

// Get base64 encoded response from eSewa
$data = $_GET['data'] ?? null;

if (!$data) {
    die("Invalid payment response - no data received");
}

// Decode the base64 response
$decoded_data = base64_decode($data);
$response = json_decode($decoded_data, true);

if (!$response) {
    die("Invalid response format");
}

// Extract response parameters
$transaction_code = $response['transaction_code'] ?? null;
$status = $response['status'] ?? null;
$total_amount = $response['total_amount'] ?? null;
$transaction_uuid = $response['transaction_uuid'] ?? null;
$product_code = $response['product_code'] ?? null;
$response_signature = $response['signature'] ?? null;

// Get pending booking from session
$pending = $_SESSION['pending_booking'] ?? null;

if (!$pending || $pending['transaction_uuid'] !== $transaction_uuid) {
    die("Invalid booking session or transaction mismatch");
}

// Verify signature to ensure response integrity
$secret_key = "8gBm/:&EnhH.1/q"; // Test secret key
$signed_field_names = $response['signed_field_names'] ?? '';
$message = "transaction_code=" . $transaction_code . ",status=" . $status . ",total_amount=" . $total_amount . ",transaction_uuid=" . $transaction_uuid . ",product_code=" . $product_code . ",signed_field_names=" . $signed_field_names;
$calculated_signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

if ($calculated_signature !== $response_signature) {
    die("Signature verification failed - potential fraud detected");
}

// Verify payment status
if ($status !== 'COMPLETE') {
    die("Payment not completed. Status: " . htmlspecialchars($status));
}

// Verify amount matches
if (floatval($total_amount) != floatval($pending['amount'])) {
    die("Amount mismatch. Expected: " . $pending['amount'] . ", Received: " . $total_amount);
}

// Double-check with eSewa status API
$verify_url = "https://rc.esewa.com.np/api/epay/transaction/status/?product_code=" . urlencode($product_code) . "&total_amount=" . urlencode($total_amount) . "&transaction_uuid=" . urlencode($transaction_uuid);

$verify_response = file_get_contents($verify_url);
$verify_data = json_decode($verify_response, true);

if (!$verify_data || $verify_data['status'] !== 'COMPLETE') {
    die("Payment verification with eSewa failed. Please contact support with Transaction ID: " . $transaction_uuid);
}

// Payment verified successfully - Save booking
try {
    $pdo->beginTransaction();
    
    // Check seat availability again
    $checkStmt = $pdo->prepare("SELECT seat_numbers FROM bookings WHERE route_id = ? AND bus_id = ?");
    $checkStmt->execute([$pending['route_id'], $pending['bus_id']]);
    $bookedSeatsRaw = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $bookedSeats = [];
    foreach ($bookedSeatsRaw as $seatList) {
        $bookedSeats = array_merge($bookedSeats, explode(',', $seatList));
    }
    
    // Check conflicts
    $conflict = array_intersect($pending['seats'], $bookedSeats);
    if (!empty($conflict)) {
        $pdo->rollBack();
        die("Seats already booked: " . implode(', ', $conflict) . ". Your payment will be refunded.");
    }
    
    // Insert booking
    $seatList = implode(",", $pending['seats']);
    
   $stmt = $pdo->prepare("
    INSERT INTO bookings 
    (user_id, route_id, bus_id, seat_numbers, total_price, payment_status, payment_token, booked_on) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");
    $stmt->execute([
        $_SESSION['user_id'],
        $pending['route_id'],
        $pending['bus_id'],
        $seatList,
        $pending['amount'],
        'verified',
        $transaction_code // Store eSewa transaction code
    ]);
    
    $bookingId = $pdo->lastInsertId();
    
    $pdo->commit();
    
    // Clear pending booking
    unset($_SESSION['pending_booking']);
    
    // Redirect to success page
    header("Location: success.php?booking_id=" . $bookingId);
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Booking Error: " . $e->getMessage());
    die("Error saving booking: " . $e->getMessage() . ". Your payment was successful. Please contact support with Transaction Code: " . $transaction_code);
}
?>