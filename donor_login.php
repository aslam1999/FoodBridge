<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  // Find user by email and role
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'donor'");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    // Set session variables
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['phone']      = $user['phone'];
    $_SESSION['address']    = $user['address'];
    $_SESSION['city']       = $user['city'];
    $_SESSION['postal']     = $user['postal_code'];
    $_SESSION['role']       = 'donor';

    header('Location: donor-dashboard.php');
    exit;
  } else {
    header('Location: donor-login.html?error=invalid_credentials');
    exit;
  }
}
?>