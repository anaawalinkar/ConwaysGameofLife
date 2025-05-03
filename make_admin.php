<?php
require 'config.php';

$username = "admin"; 
$password = "admin123"; 
$role = "admin";
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed, $role);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "✅ Admin user created.";
} else {
    echo "❌ Failed to create admin.";
}

$stmt->close();
$conn->close();

?>