<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
  echo json_encode([
    'loggedIn' => true,
    'name'     => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'email'    => $_SESSION['email'],
    'phone'    => $_SESSION['phone'],
    'address'  => $_SESSION['address'],
    'city'     => $_SESSION['city'],
    'postal'   => $_SESSION['postal'],
    'role'     => $_SESSION['role']
  ]);
} else {
  echo json_encode(['loggedIn' => false]);
}
?>