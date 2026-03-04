<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
  echo json_encode(['success' => false]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $assignment_id = $_POST['assignment_id'];
  $status        = $_POST['status'];

  // Update donation status
  $stmt = $pdo->prepare("
    UPDATE donations d
    JOIN assignments a ON a.donation_id = d.id
    SET d.status = ?
    WHERE a.id = ?
  ");
  $stmt->execute([$status, $assignment_id]);

  echo json_encode(['success' => true]);
}
?>