<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
  $stmt->execute([$email]);
  $admin = $stmt->fetch();

  if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['email']    = $admin['email'];
    $_SESSION['role']     = 'admin';

    header('Location: admin-dashboard.php');
    exit;
  } else {
    header('Location: admin-login.html?error=invalid_credentials');
    exit;
  }
}
?>