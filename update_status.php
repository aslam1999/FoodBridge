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

  // First get the donation_id from the assignment
  $stmt = $pdo->prepare("SELECT donation_id FROM assignments WHERE id = ?");
  $stmt->execute([$assignment_id]);
  $assignment = $stmt->fetch();

  if ($assignment) {
    // Update donation status directly
    $stmt = $pdo->prepare("UPDATE donations SET status = ? WHERE id = ?");
    $stmt->execute([$status, $assignment['donation_id']]);

    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false]);
  }
}
?>