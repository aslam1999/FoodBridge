<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $first_name   = trim($_POST['first_name']);
  $last_name    = trim($_POST['last_name']);
  $email        = trim($_POST['email']);
  $phone        = trim($_POST['phone']);
  $address      = trim($_POST['address']);
  $city         = trim($_POST['city']);
  $postal_code  = trim($_POST['postal_code']);
  $password     = $_POST['password'];
  $role         = $_POST['role'];
  $availability = isset($_POST['availability']) ? trim($_POST['availability']) : null;
  $service_area = isset($_POST['service_area']) ? trim($_POST['service_area']) : null;

  // Check if email already exists
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    header('Location: register.html?error=email_exists');
    exit;
  }

  // Hash password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("
    INSERT INTO users 
    (first_name, last_name, email, phone, address, city, postal_code, password, role, availability, service_area)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $first_name, $last_name, $email, $phone,
    $address, $city, $postal_code, $hashed_password,
    $role, $availability, $service_area
  ]);

  // Redirect to login after successful registration
  header('Location: donor-login.html?success=registered');
  exit;
}
?>