<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $food_type     = trim($_POST['food_type']);
  $quantity      = trim($_POST['quantity']);
  $category      = trim($_POST['category']);
  $expiry_date   = $_POST['expiry_date'];
  $pickup_address = trim($_POST['pickup_address']);
  $city          = trim($_POST['city']);
  $postal_code   = trim($_POST['postal_code']);
  $pickup_date   = $_POST['pickup_date'];
  $pickup_time   = $_POST['pickup_time'];
  $notes         = trim($_POST['notes']);
  $donor_name    = trim($_POST['donor_name']);
  $donor_email   = trim($_POST['donor_email']);
  $donor_phone   = trim($_POST['donor_phone']);

  // If logged in, use session data instead
  $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
  if ($user_id) {
    $donor_name  = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    $donor_email = $_SESSION['email'];
    $donor_phone = $_SESSION['phone'];
  }

  $stmt = $pdo->prepare("
    INSERT INTO donations 
    (food_type, quantity, category, expiry_date, pickup_address, city, postal_code, pickup_date, pickup_time, notes, donor_name, donor_email, donor_phone, user_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $food_type, $quantity, $category, $expiry_date,
    $pickup_address, $city, $postal_code, $pickup_date,
    $pickup_time, $notes, $donor_name, $donor_email,
    $donor_phone, $user_id
  ]);

  header('Location: confirmation.html');
  exit;
}
?>