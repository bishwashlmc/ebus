<?php
$host = 'localhost';
$dbname = 'eBus';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/* ---------------------------------------------------------
   eSewa Payment Configuration (Development/UAT Environment)
   --------------------------------------------------------- */

// Test merchant code for eSewa sandbox
define("ESEWA_MERCHANT_CODE", "EPAYTEST");

// UAT (testing) URLs — these work for localhost development
define("ESEWA_FORM_URL", "https://uat.esewa.com.np/epay");
define("ESEWA_VERIFY_URL", "https://uat.esewa.com.np/epay/transrec");

// When you go live, replace with production credentials:
// define("ESEWA_MERCHANT_CODE", "YOUR_LIVE_CODE_HERE");
// define("ESEWA_FORM_URL", "https://esewa.com.np/epay");
// define("ESEWA_VERIFY_URL", "https://esewa.com.np/epay/transrec");
?>
