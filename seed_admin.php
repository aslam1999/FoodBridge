<?php
require 'config.php';

$email = 'admin@foodbridge.com';
$password = 'Admin@1234';
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
$stmt->execute([$email, $hashed]);

echo "Admin created successfully!";
?>