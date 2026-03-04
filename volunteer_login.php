<?php
session_start();
require 'config.php';

var_dump($_POST);
exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'volunteer'");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['email']      = $user['email'];
    $_SESSION['phone']      = $user['phone'];
    $_SESSION['role']       = 'volunteer';

    header('Location: volunteer-dashboard.php');
    exit;
  } else {
    header('Location: volunteer-login.html?error=invalid_credentials');
    exit;
  }
}
?>