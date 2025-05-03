<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Not logged in";
    exit;
}

$user_id = $_SESSION['user_id'];
$start = $_POST['start_time'] ?? null;
$end = $_POST['end_time'] ?? null;
$generations = isset($_POST['generations']) ? intval($_POST['generations']) : null;

if (!$start || !$end || $generations === null) {
    http_response_code(400);
    echo "Missing required fields.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO game_sessions (user_id, start_time, end_time, generations) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("issi", $user_id, $start, $end, $generations);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Execute failed: " . $stmt->error;
    exit;
}

$stmt->close();
echo "Session logged!";
?>
