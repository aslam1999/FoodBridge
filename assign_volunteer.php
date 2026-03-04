<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
  header('Location: admin-login.html');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $donation_id  = $_POST['donation_id'];
  $volunteer_id = $_POST['volunteer_id'];

  // Insert into assignments table
  $stmt = $pdo->prepare("INSERT INTO assignments (donation_id, volunteer_id) VALUES (?, ?)");
  $stmt->execute([$donation_id, $volunteer_id]);

  // Update donation status to assigned
  $stmt = $pdo->prepare("UPDATE donations SET status = 'assigned' WHERE id = ?");
  $stmt->execute([$donation_id]);

  header('Location: admin-dashboard.php');
  exit;
}
?>